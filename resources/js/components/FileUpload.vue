<template>
  <div class="file-upload">
    <div 
      class="upload-zone"
      :class="{ 'dragover': isDragover, 'has-file': selectedFile }"
      @drop.prevent="handleDrop"
      @dragover.prevent="isDragover = true"
      @dragleave.prevent="isDragover = false"
      @click="triggerFileInput"
    >
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <div v-if="!selectedFile" class="upload-prompt">
        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        <p class="text-sm text-gray-600">
          Haz clic para seleccionar o arrastra una imagen aquí
        </p>
        <p class="text-xs text-gray-400 mt-1">
          PNG, JPG, GIF hasta 2MB
        </p>
      </div>
      
      <div v-else class="file-preview">
        <img :src="previewUrl" alt="Preview" class="w-24 h-24 object-cover rounded mx-auto mb-2" />
        <p class="text-sm text-gray-600 truncate">{{ selectedFile.name }}</p>
        <p class="text-xs text-gray-400">{{ formatFileSize(selectedFile.size) }}</p>
      </div>
    </div>
    
    <div v-if="selectedFile" class="flex gap-2 mt-4">
      <button
        @click="uploadFile"
        :disabled="uploading"
        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="uploading">Subiendo...</span>
        <span v-else>Subir Imagen</span>
      </button>
      <button
        @click="clearFile"
        :disabled="uploading"
        class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50"
      >
        Cancelar
      </button>
    </div>
    
    <div v-if="error" class="mt-2 text-sm text-red-600">
      {{ error }}
    </div>
    
    <div v-if="success" class="mt-2 text-sm text-green-600">
      {{ success }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Props {
  maxSize?: number // en bytes
  accept?: string[]
}

interface Emits {
  (e: 'upload', file: File): void
  (e: 'success', result: any): void
  (e: 'error', error: string): void
}

const props = withDefaults(defineProps<Props>(), {
  maxSize: 2 * 1024 * 1024, // 2MB
  accept: () => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
})

const emit = defineEmits<Emits>()

const fileInput = ref<HTMLInputElement>()
const selectedFile = ref<File | null>(null)
const isDragover = ref(false)
const uploading = ref(false)
const error = ref('')
const success = ref('')

const previewUrl = computed(() => {
  if (selectedFile.value) {
    return URL.createObjectURL(selectedFile.value)
  }
  return undefined
})

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    selectFile(target.files[0])
  }
}

const handleDrop = (event: DragEvent) => {
  isDragover.value = false
  if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
    selectFile(event.dataTransfer.files[0])
  }
}

const selectFile = (file: File) => {
  error.value = ''
  success.value = ''
  
  // Validar tipo de archivo
  if (!props.accept.includes(file.type)) {
    error.value = 'Tipo de archivo no válido. Solo se permiten imágenes.'
    return
  }
  
  // Validar tamaño
  if (file.size > props.maxSize) {
    error.value = `El archivo es demasiado grande. Máximo ${formatFileSize(props.maxSize)}.`
    return
  }
  
  selectedFile.value = file
}

const uploadFile = async () => {
  if (!selectedFile.value) return
  
  uploading.value = true
  error.value = ''
  success.value = ''
  
  try {
    emit('upload', selectedFile.value)
  } catch (err) {
    error.value = 'Error al subir el archivo'
    emit('error', error.value)
  } finally {
    uploading.value = false
  }
}

const clearFile = () => {
  selectedFile.value = null
  error.value = ''
  success.value = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Exponer métodos para uso externo
defineExpose({
  clearFile,
  setSuccess: (message: string) => { success.value = message },
  setError: (message: string) => { error.value = message },
  setUploading: (state: boolean) => { uploading.value = state }
})
</script>

<style scoped>
.upload-zone {
  border: 2px dashed #d1d5db;
  border-radius: 0.5rem;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.upload-zone.dragover {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.upload-zone.has-file {
  border-color: #10b981;
  background-color: #f0fdf4;
}

.upload-zone:hover {
  border-color: #9ca3af;
}
</style>
