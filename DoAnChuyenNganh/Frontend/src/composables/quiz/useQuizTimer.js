import { ref, computed, onUnmounted } from 'vue'

/**
 * Composable for quiz timer functionality
 * Handles countdown, auto-submit, and time formatting
 */
export function useQuizTimer(options = {}) {
  const {
    durationMinutes = 60,
    warningThreshold = 300, // 5 minutes in seconds
    onTimeUp = null,
    autoSubmit = true
  } = options

  // State
  const timeLeft = ref(durationMinutes * 60) // Convert minutes to seconds
  const isRunning = ref(false)
  const isPaused = ref(false)
  let timerInterval = null

  // Computed
  const isWarning = computed(() => timeLeft.value <= warningThreshold && timeLeft.value > 0)
  
  const isTimeUp = computed(() => timeLeft.value <= 0)
  
  const minutes = computed(() => Math.floor(timeLeft.value / 60))
  
  const seconds = computed(() => timeLeft.value % 60)
  
  const formattedTime = computed(() => {
    return `${minutes.value}:${seconds.value.toString().padStart(2, '0')}`
  })
  
  const percentage = computed(() => {
    const total = durationMinutes * 60
    return Math.max(0, Math.min(100, (timeLeft.value / total) * 100))
  })

  const elapsedTime = computed(() => {
    return (durationMinutes * 60) - timeLeft.value
  })

  const formattedElapsedTime = computed(() => {
    const elapsed = elapsedTime.value
    const mins = Math.floor(elapsed / 60)
    const secs = elapsed % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  })

  // Methods
  const start = () => {
    if (isRunning.value) return

    isRunning.value = true
    isPaused.value = false

    timerInterval = setInterval(() => {
      if (timeLeft.value > 0) {
        timeLeft.value--
      } else {
        // Time is up
        stop()
        
        if (autoSubmit && onTimeUp) {
          onTimeUp()
        }
      }
    }, 1000)
  }

  const stop = () => {
    if (timerInterval) {
      clearInterval(timerInterval)
      timerInterval = null
    }
    isRunning.value = false
    isPaused.value = false
  }

  const pause = () => {
    if (!isRunning.value || isPaused.value) return

    if (timerInterval) {
      clearInterval(timerInterval)
      timerInterval = null
    }
    isPaused.value = true
    isRunning.value = false
  }

  const resume = () => {
    if (!isPaused.value) return
    
    start()
  }

  const reset = (newDurationMinutes = null) => {
    stop()
    const duration = newDurationMinutes !== null ? newDurationMinutes : durationMinutes
    timeLeft.value = duration * 60
  }

  const addTime = (minutes) => {
    timeLeft.value += minutes * 60
  }

  const setTime = (seconds) => {
    timeLeft.value = Math.max(0, seconds)
  }

  // Format time helper (static)
  const formatTime = (totalSeconds) => {
    const mins = Math.floor(totalSeconds / 60)
    const secs = totalSeconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  // Format time with hours (static)
  const formatTimeWithHours = (totalSeconds) => {
    const hours = Math.floor(totalSeconds / 3600)
    const mins = Math.floor((totalSeconds % 3600) / 60)
    const secs = totalSeconds % 60

    if (hours > 0) {
      return `${hours}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    }
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  // Cleanup on unmount
  onUnmounted(() => {
    stop()
  })

  return {
    // State
    timeLeft,
    isRunning,
    isPaused,
    
    // Computed
    isWarning,
    isTimeUp,
    minutes,
    seconds,
    formattedTime,
    percentage,
    elapsedTime,
    formattedElapsedTime,
    
    // Methods
    start,
    stop,
    pause,
    resume,
    reset,
    addTime,
    setTime,
    formatTime,
    formatTimeWithHours
  }
}
