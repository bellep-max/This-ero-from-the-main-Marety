import { reactive, ref } from 'vue'
import apiClient from '@/api/client'
import { useRouter } from 'vue-router'

interface FormOptions {
  onSuccess?: (response: any) => void
  onError?: (errors: any) => void
  onFinish?: () => void
  preserveScroll?: boolean
  preserveState?: boolean
}

export function useForm<T extends Record<string, any>>(initialData: T) {
  const router = useRouter()
  const processing = ref(false)
  const errors = reactive<Record<string, string>>({})
  const recentlySuccessful = ref(false)
  const wasSuccessful = ref(false)
  let defaults = { ...initialData }

  const form = reactive({
    ...initialData,
    processing,
    errors,
    recentlySuccessful,
    wasSuccessful,

    data() {
      const data: Record<string, any> = {}
      for (const key of Object.keys(defaults)) {
        data[key] = (form as any)[key]
      }
      return data as T
    },

    transform(callback: (data: T) => any) {
      const self = { ...form }
      const originalPost = form.post.bind(form)
      const originalPut = form.put.bind(form)
      const originalPatch = form.patch.bind(form)
      const originalDelete = form.delete.bind(form)

      return {
        ...self,
        post(url: string, options?: FormOptions) {
          return sendRequest('post', url, callback(form.data()), options)
        },
        put(url: string, options?: FormOptions) {
          return sendRequest('put', url, callback(form.data()), options)
        },
        patch(url: string, options?: FormOptions) {
          return sendRequest('patch', url, callback(form.data()), options)
        },
        delete(url: string, options?: FormOptions) {
          return sendRequest('delete', url, callback(form.data()), options)
        },
      }
    },

    reset(...fields: string[]) {
      if (fields.length === 0) {
        Object.assign(form, { ...defaults })
      } else {
        for (const field of fields) {
          if (field in defaults) {
            (form as any)[field] = (defaults as any)[field]
          }
        }
      }
      form.clearErrors()
    },

    clearErrors(...fields: string[]) {
      if (fields.length === 0) {
        Object.keys(errors).forEach(key => delete errors[key])
      } else {
        for (const field of fields) {
          delete errors[field]
        }
      }
    },

    setError(field: string | Record<string, string>, value?: string) {
      if (typeof field === 'object') {
        Object.assign(errors, field)
      } else if (value) {
        errors[field] = value
      }
    },

    async post(url: string, options?: FormOptions) {
      return sendRequest('post', url, form.data(), options)
    },

    async put(url: string, options?: FormOptions) {
      return sendRequest('put', url, form.data(), options)
    },

    async patch(url: string, options?: FormOptions) {
      return sendRequest('patch', url, form.data(), options)
    },

    async delete(url: string, options?: FormOptions) {
      return sendRequest('delete', url, undefined, options)
    },

    async get(url: string, options?: FormOptions) {
      return sendRequest('get', url, undefined, options)
    },
  })

  async function sendRequest(method: string, url: string, data?: any, options?: FormOptions) {
    processing.value = true
    Object.keys(errors).forEach(key => delete errors[key])

    try {
      const isFormData = data instanceof FormData
      const config: any = {}
      if (isFormData) {
        config.headers = { 'Content-Type': 'multipart/form-data' }
      }

      let response: any
      if (method === 'get' || method === 'delete') {
        response = await (apiClient as any)[method](url, config)
      } else {
        response = await (apiClient as any)[method](url, data, config)
      }

      wasSuccessful.value = true
      recentlySuccessful.value = true
      setTimeout(() => { recentlySuccessful.value = false }, 2000)

      if (options?.onSuccess) {
        options.onSuccess(response)
      }
      return response
    } catch (err: any) {
      wasSuccessful.value = false
      if (err.response?.status === 422 && err.response?.data?.errors) {
        const serverErrors = err.response.data.errors
        for (const [key, messages] of Object.entries(serverErrors)) {
          errors[key] = Array.isArray(messages) ? (messages as string[])[0] : messages as string
        }
      }
      if (options?.onError) {
        options.onError(err.response?.data?.errors || err)
      }
    } finally {
      processing.value = false
      if (options?.onFinish) {
        options.onFinish()
      }
    }
  }

  return form
}

export default useForm
