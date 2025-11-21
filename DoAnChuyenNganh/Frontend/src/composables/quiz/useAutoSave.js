import { ref, onUnmounted } from 'vue'
import api from '@/services/api'

/**
 * Composable for auto-saving quiz progress
 * Periodically saves answers to backend to prevent data loss
 */
export function useAutoSave(options = {}) {
  const {
    interval = 30000, // Default: 30 seconds
    onSave = null,
    onError = null
  } = options

  // State
  const isSaving = ref(false)
  const lastSavedAt = ref(null)
  const saveCount = ref(0)
  const error = ref(null)
  let autoSaveInterval = null

  /**
   * Start auto-save timer
   * @param {Function} saveCallback - Callback function to execute on each save
   */
  const start = (saveCallback) => {
    if (autoSaveInterval) return // Already running

    autoSaveInterval = setInterval(async () => {
      await save(saveCallback)
    }, interval)
  }

  /**
   * Stop auto-save timer
   */
  const stop = () => {
    if (autoSaveInterval) {
      clearInterval(autoSaveInterval)
      autoSaveInterval = null
    }
  }

  /**
   * Execute save operation
   * @param {Function} saveCallback - Callback to get save data
   */
  const save = async (saveCallback) => {
    if (isSaving.value) return // Skip if already saving

    isSaving.value = true
    error.value = null

    try {
      const data = typeof saveCallback === 'function' ? saveCallback() : saveCallback

      if (!data) {
        isSaving.value = false
        return
      }

      // Execute custom onSave callback if provided
      if (onSave) {
        await onSave(data)
      }

      lastSavedAt.value = new Date()
      saveCount.value++
      
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu tự động'
      
      if (onError) {
        onError(err)
      }
      
      console.error('Auto-save error:', err)
    } finally {
      isSaving.value = false
    }
  }

  /**
   * Save quiz progress to API
   * @param {Object} payload - Save payload
   * @param {number} payload.attemptId - Quiz attempt ID
   * @param {Object} payload.answers - User answers
   * @param {Array} payload.markedQuestions - Marked question IDs
   * @param {number} payload.currentQuestionIndex - Current question index
   */
  const saveToAPI = async (payload) => {
    const { attemptId, answers, markedQuestions = [], currentQuestionIndex = 0 } = payload

    if (!attemptId) {
      throw new Error('attemptId is required for auto-save')
    }

    isSaving.value = true
    error.value = null

    try {
      const response = await api.patch(`/api/quiz/attempt/${attemptId}/auto-save`, {
        answers,
        marked_questions: markedQuestions,
        current_question_index: currentQuestionIndex,
        saved_at: new Date().toISOString()
      })

      lastSavedAt.value = new Date()
      saveCount.value++

      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Lỗi khi lưu tự động'
      throw err
    } finally {
      isSaving.value = false
    }
  }

  /**
   * Save to localStorage as backup
   * @param {Object} data - Data to save
   * @param {string} key - Storage key
   */
  const saveToLocalStorage = (data, key = 'quiz_autosave') => {
    try {
      const saveData = {
        ...data,
        savedAt: new Date().toISOString(),
        version: '1.0'
      }
      
      localStorage.setItem(key, JSON.stringify(saveData))
      lastSavedAt.value = new Date()
      saveCount.value++
      
      return true
    } catch (err) {
      error.value = 'Lỗi khi lưu vào localStorage'
      console.error('LocalStorage save error:', err)
      return false
    }
  }

  /**
   * Load from localStorage
   * @param {string} key - Storage key
   */
  const loadFromLocalStorage = (key = 'quiz_autosave') => {
    try {
      const saved = localStorage.getItem(key)
      if (!saved) return null
      
      return JSON.parse(saved)
    } catch (err) {
      console.error('LocalStorage load error:', err)
      return null
    }
  }

  /**
   * Clear localStorage
   * @param {string} key - Storage key
   */
  const clearLocalStorage = (key = 'quiz_autosave') => {
    try {
      localStorage.removeItem(key)
      return true
    } catch (err) {
      console.error('LocalStorage clear error:', err)
      return false
    }
  }

  /**
   * Manual save trigger
   * @param {Function|Object} saveCallback - Data or callback
   */
  const saveNow = async (saveCallback) => {
    return await save(saveCallback)
  }

  /**
   * Get time since last save
   */
  const getTimeSinceLastSave = () => {
    if (!lastSavedAt.value) return null
    
    const diff = Date.now() - lastSavedAt.value.getTime()
    return Math.floor(diff / 1000) // Return in seconds
  }

  /**
   * Get formatted last save time
   */
  const getFormattedLastSave = () => {
    if (!lastSavedAt.value) return 'Chưa lưu'
    
    return lastSavedAt.value.toLocaleTimeString('vi-VN')
  }

  /**
   * Reset auto-save state
   */
  const reset = () => {
    stop()
    isSaving.value = false
    lastSavedAt.value = null
    saveCount.value = 0
    error.value = null
  }

  // Cleanup on unmount
  onUnmounted(() => {
    stop()
  })

  return {
    // State
    isSaving,
    lastSavedAt,
    saveCount,
    error,

    // Methods
    start,
    stop,
    save,
    saveToAPI,
    saveToLocalStorage,
    loadFromLocalStorage,
    clearLocalStorage,
    saveNow,
    getTimeSinceLastSave,
    getFormattedLastSave,
    reset
  }
}
