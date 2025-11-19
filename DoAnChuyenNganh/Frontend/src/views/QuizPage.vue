<template>
  <div class="quiz-container">
    <!-- Quiz Header -->
    <div class="quiz-header">
      <div class="quiz-info">
        <h1 class="quiz-title">{{ quiz.title }}</h1>
        <p class="quiz-description">{{ quiz.description }}</p>
        <div class="quiz-meta">
          <span class="meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ quiz.duration_minutes }} phút
          </span>
          <span class="meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
            </svg>
            {{ quiz.total_questions }} câu hỏi
          </span>
          <span class="meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            Điểm đạt: {{ quiz.passing_score }}%
          </span>
          <span class="meta-item" v-if="quiz.attempts_left">
            Còn {{ quiz.attempts_left }} lần làm
          </span>
        </div>
      </div>

      <!-- Timer và Progress -->
      <div class="quiz-controls" v-if="quizStarted">
        <div class="timer" :class="{ 'timer-warning': timeLeft < 300 }">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <span class="timer-text">{{ formatTime(timeLeft) }}</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
          <span class="progress-text">{{ answeredCount }}/{{ quiz.total_questions }}</span>
        </div>
      </div>
    </div>

    <!-- Start Screen -->
    <div v-if="!quizStarted" class="start-screen">
      <div class="instructions">
        <h2>📋 Hướng dẫn làm bài</h2>
        <ul>
          <li>Bạn có <strong>{{ quiz.duration_minutes }} phút</strong> để hoàn thành {{ quiz.total_questions }} câu hỏi</li>
          <li>Điểm đạt: <strong>{{ quiz.passing_score }}%</strong></li>
          <li>Bài kiểm tra sẽ tự động nộp khi hết giờ</li>
          <li>Bạn có thể đánh dấu câu hỏi để xem lại sau</li>
          <li>Câu trả lời được tự động lưu mỗi 30 giây</li>
          <li>Không được refresh hoặc thoát tab khi đang làm bài</li>
        </ul>
      </div>
      <button @click="startQuiz" class="btn-start">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
        </svg>
        Bắt đầu làm bài
      </button>
    </div>

    <!-- Quiz Content -->
    <div v-else-if="!quizSubmitted" class="quiz-content">
      <!-- Question Navigator -->
      <div class="question-navigator">
        <div class="nav-title">Danh sách câu hỏi</div>
        <div class="nav-grid">
          <button
            v-for="(q, index) in questions"
            :key="q.id"
            @click="currentQuestionIndex = index"
            class="nav-item"
            :class="{
              'active': currentQuestionIndex === index,
              'answered': userAnswers[q.id] !== undefined && userAnswers[q.id] !== null && userAnswers[q.id] !== '',
              'marked': markedQuestions.includes(q.id)
            }"
          >
            {{ index + 1 }}
          </button>
        </div>
        <div class="nav-legend">
          <div class="legend-item"><span class="dot answered"></span> Đã trả lời</div>
          <div class="legend-item"><span class="dot marked"></span> Đánh dấu</div>
          <div class="legend-item"><span class="dot"></span> Chưa trả lời</div>
        </div>
      </div>

      <!-- Current Question -->
      <div class="question-area">
        <div class="question-card" v-if="currentQuestion">
          <div class="question-header">
            <div class="question-number">Câu {{ currentQuestionIndex + 1 }}/{{ questions.length }}</div>
            <button @click="toggleMark(currentQuestion.id)" class="btn-mark" :class="{ 'marked': markedQuestions.includes(currentQuestion.id) }">
              <svg xmlns="http://www.w3.org/2000/svg" :fill="markedQuestions.includes(currentQuestion.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
              </svg>
              {{ markedQuestions.includes(currentQuestion.id) ? 'Bỏ đánh dấu' : 'Đánh dấu' }}
            </button>
          </div>

          <div class="question-content">
            <div class="question-type-badge" :class="'type-' + currentQuestion.type">
              {{ getQuestionTypeLabel(currentQuestion.type) }}
            </div>
            <div class="question-points">{{ currentQuestion.points }} điểm</div>
            
            <div class="question-text" v-html="currentQuestion.text"></div>
            <img v-if="currentQuestion.image" :src="currentQuestion.image" class="question-image" />

            <!-- Multiple Choice -->
            <div v-if="currentQuestion.type === 'multiple_choice'" class="options-list">
              <label
                v-for="option in currentQuestion.options"
                :key="option.id"
                class="option-item"
                :class="{ 'selected': isOptionSelected(currentQuestion.id, option.id) }"
              >
                <input
                  type="checkbox"
                  :value="option.id"
                  v-model="userAnswers[currentQuestion.id]"
                  @change="onAnswerChange(currentQuestion.id)"
                />
                <span class="option-label">{{ option.text }}</span>
                <img v-if="option.image" :src="option.image" class="option-image" />
              </label>
            </div>

            <!-- True/False -->
            <div v-else-if="currentQuestion.type === 'true_false'" class="options-list">
              <label
                v-for="option in currentQuestion.options"
                :key="option.id"
                class="option-item"
                :class="{ 'selected': userAnswers[currentQuestion.id] === option.id }"
              >
                <input
                  type="radio"
                  :name="'question-' + currentQuestion.id"
                  :value="option.id"
                  v-model="userAnswers[currentQuestion.id]"
                  @change="onAnswerChange(currentQuestion.id)"
                />
                <span class="option-label">{{ option.text }}</span>
              </label>
            </div>

            <!-- Fill Blank -->
            <div v-else-if="currentQuestion.type === 'fill_blank'" class="fill-blank">
              <input
                type="text"
                v-model="userAnswers[currentQuestion.id]"
                @input="onAnswerChange(currentQuestion.id)"
                placeholder="Nhập câu trả lời của bạn..."
                class="fill-input"
              />
            </div>

            <!-- Matching -->
            <div v-else-if="currentQuestion.type === 'matching'" class="matching-area">
              <div class="matching-instruction">Kéo thả để ghép cặp đáp án</div>
              <div class="matching-grid">
                <div class="matching-left">
                  <div v-for="(item, index) in currentQuestion.leftItems" :key="index" class="matching-item">
                    {{ item }}
                  </div>
                </div>
                <div class="matching-right">
                  <div v-for="(item, index) in currentQuestion.rightItems" :key="index" class="matching-item">
                    <select v-model="userAnswers[currentQuestion.id][index]" @change="onAnswerChange(currentQuestion.id)" class="matching-select">
                      <option value="">-- Chọn --</option>
                      <option v-for="(rightItem, rIndex) in currentQuestion.rightItems" :key="rIndex" :value="rIndex">
                        {{ rightItem }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Essay/Code -->
            <div v-else-if="currentQuestion.type === 'essay' || currentQuestion.type === 'code'" class="essay-area">
              <textarea
                v-model="userAnswers[currentQuestion.id]"
                @input="onAnswerChange(currentQuestion.id)"
                :placeholder="currentQuestion.type === 'code' ? 'Nhập code của bạn...' : 'Nhập câu trả lời của bạn...'"
                class="essay-textarea"
                rows="10"
              ></textarea>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="question-nav-buttons">
            <button @click="previousQuestion" :disabled="currentQuestionIndex === 0" class="btn-nav btn-prev">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
              </svg>
              Câu trước
            </button>
            <button @click="nextQuestion" :disabled="currentQuestionIndex === questions.length - 1" class="btn-nav btn-next">
              Câu sau
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="submit-area">
          <button @click="showSubmitConfirm = true" class="btn-submit">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Nộp bài
          </button>
        </div>
      </div>
    </div>

    <!-- Result Screen -->
    <div v-else class="result-screen">
      <div class="result-card">
        <div class="result-icon" :class="result.passed ? 'passed' : 'failed'">
          <svg v-if="result.passed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-24 h-24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-24 h-24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
          </svg>
        </div>

        <h1 class="result-title">{{ result.passed ? 'Chúc mừng! Bạn đã đạt' : 'Rất tiếc! Bạn chưa đạt' }}</h1>
        
        <div class="result-score">{{ result.score }}/100</div>
        
        <div class="result-stats">
          <div class="stat-item">
            <div class="stat-label">Số câu đúng</div>
            <div class="stat-value">{{ result.correct_answers }}/{{ result.total_questions }}</div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Thời gian làm bài</div>
            <div class="stat-value">{{ result.time_taken }}</div>
          </div>
          <div class="stat-item">
            <div class="stat-label">Điểm đạt</div>
            <div class="stat-value">{{ quiz.passing_score }}%</div>
          </div>
        </div>

        <!-- Detailed Results -->
        <div class="detailed-results">
          <h3>Chi tiết kết quả</h3>
          <div class="result-list">
            <div
              v-for="(detail, index) in result.details"
              :key="index"
              class="result-item"
              :class="detail.is_correct ? 'correct' : 'incorrect'"
            >
              <div class="result-item-header">
                <span class="result-question-number">Câu {{ index + 1 }}</span>
                <span class="result-points">{{ detail.points_earned }}/{{ detail.points_max }} điểm</span>
              </div>
              <div class="result-question-text">{{ detail.question_text }}</div>
              <div class="result-answer">
                <div class="your-answer">
                  <strong>Câu trả lời của bạn:</strong> 
                  <span :class="detail.is_correct ? 'text-green-600' : 'text-red-600'">
                    {{ detail.your_answer }}
                  </span>
                </div>
                <div class="correct-answer" v-if="!detail.is_correct">
                  <strong>Đáp án đúng:</strong> 
                  <span class="text-green-600">{{ detail.correct_answer }}</span>
                </div>
              </div>
              <div class="result-explanation" v-if="detail.explanation">
                <strong>💡 Giải thích:</strong> {{ detail.explanation }}
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="result-actions">
          <button @click="retryQuiz" v-if="quiz.attempts_left > 0" class="btn-retry">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Làm lại (Còn {{ quiz.attempts_left }} lần)
          </button>
          <button @click="backToCourse" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            Quay lại khóa học
          </button>
          <button @click="downloadCertificate" v-if="result.passed" class="btn-certificate">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            Tải chứng chỉ
          </button>
        </div>
      </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div v-if="showSubmitConfirm" class="modal-overlay" @click="showSubmitConfirm = false">
      <div class="modal-content" @click.stop>
        <h3>Xác nhận nộp bài</h3>
        <p>Bạn đã trả lời <strong>{{ answeredCount }}/{{ questions.length }}</strong> câu hỏi.</p>
        <p v-if="answeredCount < questions.length" class="text-red-600">
          Còn <strong>{{ questions.length - answeredCount }}</strong> câu chưa trả lời!
        </p>
        <p>Bạn có chắc chắn muốn nộp bài?</p>
        <div class="modal-actions">
          <button @click="showSubmitConfirm = false" class="btn-cancel">Hủy</button>
          <button @click="submitQuiz" class="btn-confirm">Nộp bài</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// ========== STATE ==========
const quizStarted = ref(false)
const quizSubmitted = ref(false)
const currentQuestionIndex = ref(0)
const timeLeft = ref(0)
const userAnswers = ref({})
const markedQuestions = ref([])
const showSubmitConfirm = ref(false)
const attemptId = ref(null)
let timerInterval = null
let autoSaveInterval = null

// ========== MOCK DATA - Thay thế bằng API call ==========
const quiz = ref({
  id: 1,
  title: 'Kiểm tra Vue.js 3 Composition API',
  description: 'Bài kiểm tra kiến thức về Vue.js 3 và Composition API',
  duration_minutes: 30,
  total_questions: 10,
  passing_score: 70,
  attempts_left: 2,
  shuffle_questions: true,
  shuffle_answers: true
})

const questions = ref([
  {
    id: 1,
    type: 'multiple_choice',
    text: 'Những hàm nào sau đây thuộc Composition API của Vue 3? (Chọn nhiều đáp án)',
    image: null,
    points: 3,
    options: [
      { id: 1, text: 'ref()', image: null },
      { id: 2, text: 'reactive()', image: null },
      { id: 3, text: 'data()', image: null },
      { id: 4, text: 'computed()', image: null },
      { id: 5, text: 'methods()', image: null }
    ]
  },
  {
    id: 2,
    type: 'true_false',
    text: 'Vue.js 3 sử dụng Virtual DOM để tối ưu hiệu suất rendering?',
    image: null,
    points: 2,
    options: [
      { id: 6, text: 'Đúng' },
      { id: 7, text: 'Sai' }
    ]
  },
  {
    id: 3,
    type: 'fill_blank',
    text: 'Để tạo một biến reactive trong Composition API, ta sử dụng hàm ____',
    image: null,
    points: 2
  },
  {
    id: 4,
    type: 'multiple_choice',
    text: 'Lifecycle hook nào được gọi sau khi component được mount?',
    image: null,
    points: 2,
    options: [
      { id: 8, text: 'onBeforeMount()' },
      { id: 9, text: 'onMounted()' },
      { id: 10, text: 'onUpdated()' },
      { id: 11, text: 'onCreated()' }
    ]
  },
  {
    id: 5,
    type: 'true_false',
    text: 'Pinia là thư viện quản lý state chính thức của Vue 3?',
    image: null,
    points: 2,
    options: [
      { id: 12, text: 'Đúng' },
      { id: 13, text: 'Sai' }
    ]
  },
  {
    id: 6,
    type: 'fill_blank',
    text: 'Để sử dụng Composition API trong component, ta dùng thẻ script với cú pháp ____ setup',
    image: null,
    points: 2
  },
  {
    id: 7,
    type: 'multiple_choice',
    text: 'Cách nào đúng để bind class động trong Vue 3?',
    image: null,
    points: 2,
    options: [
      { id: 14, text: ':class="{ active: isActive }"' },
      { id: 15, text: 'class="{{ active }}"' },
      { id: 16, text: 'v-bind:class="active"' },
      { id: 17, text: 'ng-class="active"' }
    ]
  },
  {
    id: 8,
    type: 'true_false',
    text: 'watchEffect() tự động track dependencies và chạy lại khi dependencies thay đổi?',
    image: null,
    points: 2,
    options: [
      { id: 18, text: 'Đúng' },
      { id: 19, text: 'Sai' }
    ]
  },
  {
    id: 9,
    type: 'fill_blank',
    text: 'Router của Vue 3 được gọi là Vue ____',
    image: null,
    points: 2
  },
  {
    id: 10,
    type: 'essay',
    text: 'Giải thích sự khác biệt giữa ref() và reactive() trong Vue 3 Composition API',
    image: null,
    points: 5
  }
])

const result = ref({
  score: 0,
  passed: false,
  correct_answers: 0,
  total_questions: 0,
  time_taken: '',
  details: []
})

// ========== COMPUTED ==========
const currentQuestion = computed(() => questions.value[currentQuestionIndex.value])

const progressPercentage = computed(() => {
  return (answeredCount.value / questions.value.length) * 100
})

const answeredCount = computed(() => {
  return Object.keys(userAnswers.value).filter(key => {
    const answer = userAnswers.value[key]
    return answer !== undefined && answer !== null && answer !== ''
  }).length
})

// ========== METHODS ==========
const startQuiz = async () => {
  // TODO: Call API to start quiz and get attempt_id
  // const response = await axios.post(`/api/quiz/${quiz.value.id}/start`)
  // attemptId.value = response.data.attempt_id
  
  attemptId.value = Date.now() // Mock
  quizStarted.value = true
  timeLeft.value = quiz.value.duration_minutes * 60
  
  // Initialize answers
  questions.value.forEach(q => {
    if (q.type === 'multiple_choice') {
      userAnswers.value[q.id] = []
    } else if (q.type === 'matching') {
      userAnswers.value[q.id] = {}
    } else {
      userAnswers.value[q.id] = null
    }
  })
  
  startTimer()
  startAutoSave()
}

const startTimer = () => {
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      submitQuiz()
    }
  }, 1000)
}

const startAutoSave = () => {
  autoSaveInterval = setInterval(() => {
    autoSaveAnswers()
  }, 30000) // Auto-save every 30 seconds
}

const autoSaveAnswers = async () => {
  if (!quizStarted.value || quizSubmitted.value) return
  
  // TODO: Call API to auto-save
  // await axios.patch(`/api/quiz/attempt/${attemptId.value}/auto-save`, {
  //   answers: userAnswers.value
  // })
  
  console.log('Auto-saved at', new Date().toLocaleTimeString())
}

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const getQuestionTypeLabel = (type) => {
  const labels = {
    multiple_choice: 'Nhiều lựa chọn',
    true_false: 'Đúng/Sai',
    fill_blank: 'Điền khuyết',
    matching: 'Ghép cặp',
    essay: 'Tự luận',
    code: 'Code'
  }
  return labels[type] || type
}

const isOptionSelected = (questionId, optionId) => {
  const answer = userAnswers.value[questionId]
  return Array.isArray(answer) && answer.includes(optionId)
}

const onAnswerChange = (questionId) => {
  // Trigger reactivity
  userAnswers.value = { ...userAnswers.value }
}

const toggleMark = (questionId) => {
  const index = markedQuestions.value.indexOf(questionId)
  if (index > -1) {
    markedQuestions.value.splice(index, 1)
  } else {
    markedQuestions.value.push(questionId)
  }
}

const previousQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++
  }
}

const submitQuiz = async () => {
  showSubmitConfirm.value = false
  
  // Stop timers
  clearInterval(timerInterval)
  clearInterval(autoSaveInterval)
  
  const timeTaken = quiz.value.duration_minutes * 60 - timeLeft.value
  
  // TODO: Call API to submit quiz
  // const response = await axios.post(`/api/quiz/attempt/${attemptId.value}/submit`, {
  //   answers: userAnswers.value
  // })
  // result.value = response.data
  
  // MOCK: Calculate result
  calculateResult(timeTaken)
  
  quizSubmitted.value = true
}

const calculateResult = (timeTaken) => {
  // Mock calculation - replace with actual API response
  let correctCount = 0
  let totalPoints = 0
  let earnedPoints = 0
  const details = []
  
  questions.value.forEach(q => {
    totalPoints += q.points
    const userAnswer = userAnswers.value[q.id]
    let isCorrect = false
    let pointsEarned = 0
    let yourAnswer = ''
    let correctAnswer = ''
    
    if (q.type === 'multiple_choice') {
      // Mock: assume options 1,2,4 are correct
      const correctIds = [1, 2, 4]
      isCorrect = JSON.stringify(userAnswer?.sort()) === JSON.stringify(correctIds.sort())
      yourAnswer = q.options.filter(o => userAnswer?.includes(o.id)).map(o => o.text).join(', ') || 'Không có'
      correctAnswer = q.options.filter(o => correctIds.includes(o.id)).map(o => o.text).join(', ')
    } else if (q.type === 'true_false') {
      // Mock: assume option 6, 12, 18 are correct (all "Đúng")
      const correctId = [6, 12, 18].includes(q.options[0].id) ? q.options[0].id : q.options[1].id
      isCorrect = userAnswer === correctId
      yourAnswer = q.options.find(o => o.id === userAnswer)?.text || 'Không có'
      correctAnswer = q.options.find(o => o.id === correctId)?.text
    } else if (q.type === 'fill_blank') {
      // Mock: accept ref, reactive, <script, Router
      const validAnswers = ['ref', 'reactive', '<script', 'Router']
      isCorrect = validAnswers.some(ans => userAnswer?.toLowerCase().includes(ans.toLowerCase()))
      yourAnswer = userAnswer || 'Không có'
      correctAnswer = validAnswers.join(' hoặc ')
    } else if (q.type === 'essay') {
      // Essay needs manual grading
      isCorrect = null
      yourAnswer = userAnswer || 'Không có'
      correctAnswer = 'Chờ giảng viên chấm'
    }
    
    if (isCorrect === true) {
      correctCount++
      pointsEarned = q.points
      earnedPoints += pointsEarned
    }
    
    details.push({
      question_text: q.text,
      your_answer: yourAnswer,
      correct_answer: correctAnswer,
      is_correct: isCorrect,
      points_earned: pointsEarned,
      points_max: q.points,
      explanation: 'Giải thích chi tiết sẽ được hiển thị từ database'
    })
  })
  
  const score = Math.round((earnedPoints / totalPoints) * 100)
  
  result.value = {
    score,
    passed: score >= quiz.value.passing_score,
    correct_answers: correctCount,
    total_questions: questions.value.length,
    time_taken: formatTime(timeTaken),
    details
  }
}

const retryQuiz = () => {
  // Reset state
  quizStarted.value = false
  quizSubmitted.value = false
  currentQuestionIndex.value = 0
  userAnswers.value = {}
  markedQuestions.value = []
  quiz.value.attempts_left--
}

const backToCourse = () => {
  router.push(`/learn/${route.params.courseId}`)
}

const downloadCertificate = () => {
  alert('Tính năng tải chứng chỉ sẽ được triển khai!')
}

// ========== LIFECYCLE ==========
onMounted(() => {
  // TODO: Fetch quiz data from API
  // const quizId = route.params.quizId
  // await fetchQuizData(quizId)
  
  // Prevent page refresh
  window.addEventListener('beforeunload', handleBeforeUnload)
})

onUnmounted(() => {
  clearInterval(timerInterval)
  clearInterval(autoSaveInterval)
  window.removeEventListener('beforeunload', handleBeforeUnload)
})

const handleBeforeUnload = (e) => {
  if (quizStarted.value && !quizSubmitted.value) {
    e.preventDefault()
    e.returnValue = 'Bạn đang làm bài kiểm tra. Bạn có chắc muốn rời khỏi trang?'
  }
}
</script>

<style scoped>
.quiz-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.quiz-header {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.quiz-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1a202c;
  margin-bottom: 0.5rem;
}

.quiz-description {
  color: #718096;
  margin-bottom: 1rem;
}

.quiz-meta {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #4a5568;
  font-size: 0.875rem;
}

.meta-item svg {
  color: #667eea;
}

.quiz-controls {
  margin-top: 1.5rem;
  display: flex;
  gap: 2rem;
  align-items: center;
}

.timer {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: #f7fafc;
  border-radius: 0.5rem;
  font-size: 1.25rem;
  font-weight: bold;
  color: #2d3748;
}

.timer svg {
  color: #667eea;
}

.timer-warning {
  background: #fed7d7;
  color: #c53030;
}

.timer-warning svg {
  color: #c53030;
}

.progress-bar {
  flex: 1;
  height: 2rem;
  background: #e2e8f0;
  border-radius: 1rem;
  position: relative;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea, #764ba2);
  transition: width 0.3s ease;
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-weight: bold;
  color: #2d3748;
  font-size: 0.875rem;
}

.start-screen {
  background: white;
  border-radius: 1rem;
  padding: 3rem;
  max-width: 800px;
  margin: 0 auto;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.instructions h2 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #1a202c;
}

.instructions ul {
  list-style: none;
  padding: 0;
}

.instructions li {
  padding: 0.75rem 0;
  color: #4a5568;
  border-bottom: 1px solid #e2e8f0;
}

.instructions li:before {
  content: "✓";
  color: #48bb78;
  font-weight: bold;
  margin-right: 0.75rem;
}

.btn-start {
  margin-top: 2rem;
  width: 100%;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 1.125rem;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: transform 0.2s;
}

.btn-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.quiz-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.question-navigator {
  background: white;
  border-radius: 1rem;
  padding: 1.5rem;
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.nav-title {
  font-weight: bold;
  margin-bottom: 1rem;
  color: #1a202c;
}

.nav-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.nav-item {
  aspect-ratio: 1;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  background: white;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-item:hover {
  border-color: #667eea;
}

.nav-item.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.nav-item.answered {
  background: #48bb78;
  color: white;
  border-color: #48bb78;
}

.nav-item.marked {
  background: #ed8936;
  color: white;
  border-color: #ed8936;
}

.nav-legend {
  border-top: 1px solid #e2e8f0;
  padding-top: 1rem;
  font-size: 0.75rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  color: #718096;
}

.dot {
  width: 1rem;
  height: 1rem;
  border-radius: 0.25rem;
  border: 2px solid #e2e8f0;
  background: white;
}

.dot.answered {
  background: #48bb78;
  border-color: #48bb78;
}

.dot.marked {
  background: #ed8936;
  border-color: #ed8936;
}

.question-area {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.question-card {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e2e8f0;
}

.question-number {
  font-size: 1.25rem;
  font-weight: bold;
  color: #667eea;
}

.btn-mark {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 0.875rem;
  color: #4a5568;
  transition: all 0.2s;
}

.btn-mark:hover {
  border-color: #ed8936;
  color: #ed8936;
}

.btn-mark.marked {
  background: #ed8936;
  border-color: #ed8936;
  color: white;
}

.question-content {
  position: relative;
}

.question-type-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  font-weight: bold;
  margin-bottom: 1rem;
  background: #e2e8f0;
  color: #4a5568;
}

.type-multiple_choice {
  background: #bee3f8;
  color: #2c5282;
}

.type-true_false {
  background: #c6f6d5;
  color: #22543d;
}

.type-fill_blank {
  background: #feebc8;
  color: #7c2d12;
}

.type-essay {
  background: #e9d8fd;
  color: #44337a;
}

.question-points {
  position: absolute;
  top: 0;
  right: 0;
  background: #667eea;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: bold;
  font-size: 0.875rem;
}

.question-text {
  font-size: 1.125rem;
  line-height: 1.75;
  color: #1a202c;
  margin-bottom: 1.5rem;
}

.question-image {
  max-width: 100%;
  border-radius: 0.5rem;
  margin-bottom: 1.5rem;
}

.options-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.option-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.option-item:hover {
  border-color: #667eea;
  background: #f7fafc;
}

.option-item.selected {
  border-color: #667eea;
  background: #ebf4ff;
}

.option-item input[type="checkbox"],
.option-item input[type="radio"] {
  width: 1.25rem;
  height: 1.25rem;
  cursor: pointer;
}

.option-label {
  flex: 1;
  color: #2d3748;
  font-size: 1rem;
}

.option-image {
  max-width: 100px;
  border-radius: 0.25rem;
}

.fill-input {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.fill-input:focus {
  outline: none;
  border-color: #667eea;
}

.essay-textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-family: inherit;
  resize: vertical;
  transition: border-color 0.2s;
}

.essay-textarea:focus {
  outline: none;
  border-color: #667eea;
}

.question-nav-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 2px solid #e2e8f0;
}

.btn-nav {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 2px solid #667eea;
  background: white;
  color: #667eea;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-nav:hover:not(:disabled) {
  background: #667eea;
  color: white;
}

.btn-nav:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.submit-area {
  display: flex;
  justify-content: center;
}

.btn-submit {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 3rem;
  background: linear-gradient(135deg, #48bb78, #38a169);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 1.125rem;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(72, 187, 120, 0.4);
}

.result-screen {
  max-width: 1000px;
  margin: 0 auto;
}

.result-card {
  background: white;
  border-radius: 1rem;
  padding: 3rem;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.result-icon {
  text-align: center;
  margin-bottom: 1.5rem;
}

.result-icon.passed svg {
  color: #48bb78;
}

.result-icon.failed svg {
  color: #f56565;
}

.result-title {
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #1a202c;
}

.result-score {
  text-align: center;
  font-size: 4rem;
  font-weight: bold;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 2rem;
}

.result-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-item {
  text-align: center;
  padding: 1.5rem;
  background: #f7fafc;
  border-radius: 0.5rem;
}

.stat-label {
  color: #718096;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #2d3748;
}

.detailed-results {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 2px solid #e2e8f0;
}

.detailed-results h3 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  color: #1a202c;
}

.result-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.result-item {
  padding: 1.5rem;
  border-radius: 0.5rem;
  border-left: 4px solid;
}

.result-item.correct {
  background: #f0fff4;
  border-color: #48bb78;
}

.result-item.incorrect {
  background: #fff5f5;
  border-color: #f56565;
}

.result-item-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-weight: bold;
}

.result-question-number {
  color: #667eea;
}

.result-question-text {
  color: #2d3748;
  margin-bottom: 0.75rem;
}

.result-answer {
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.your-answer, .correct-answer {
  margin-bottom: 0.5rem;
}

.text-green-600 {
  color: #38a169;
}

.text-red-600 {
  color: #e53e3e;
}

.result-explanation {
  margin-top: 0.75rem;
  padding: 0.75rem;
  background: white;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  color: #4a5568;
}

.result-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.result-actions button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.result-actions button:hover {
  transform: translateY(-2px);
}

.btn-retry {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-back {
  background: #e2e8f0;
  color: #2d3748;
}

.btn-certificate {
  background: linear-gradient(135deg, #f6ad55, #ed8936);
  color: white;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  max-width: 500px;
  width: 90%;
}

.modal-content h3 {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #1a202c;
}

.modal-content p {
  margin-bottom: 0.75rem;
  color: #4a5568;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

.btn-cancel {
  padding: 0.75rem 1.5rem;
  background: #e2e8f0;
  color: #2d3748;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}

.btn-confirm {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #48bb78, #38a169);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  cursor: pointer;
}
</style>
