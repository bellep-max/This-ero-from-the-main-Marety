import { useState, useCallback, useRef } from 'react';
import apiClient from '@/api/client';

interface FormOptions {
    onSuccess?: (response: any) => void;
    onError?: (errors: any) => void;
    onFinish?: () => void;
    preserveScroll?: boolean;
    preserveState?: boolean;
}

export function useForm<T extends Record<string, any>>(initialData: T) {
    const [data, setData] = useState<T>({ ...initialData });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);
    const [recentlySuccessful, setRecentlySuccessful] = useState(false);
    const [wasSuccessful, setWasSuccessful] = useState(false);
    const defaults = useRef({ ...initialData });

    const isDirty = JSON.stringify(data) !== JSON.stringify(defaults.current);

    const setField = useCallback((field: string, value: any) => {
        setData((prev) => ({ ...prev, [field]: value }));
    }, []);

    const setFields = useCallback((fields: Partial<T>) => {
        setData((prev) => ({ ...prev, ...fields }));
    }, []);

    const reset = useCallback((...fields: string[]) => {
        if (fields.length === 0) {
            setData({ ...defaults.current });
        } else {
            setData((prev) => {
                const updated = { ...prev };
                for (const field of fields) {
                    if (field in defaults.current) {
                        (updated as any)[field] = (defaults.current as any)[field];
                    }
                }
                return updated;
            });
        }
        setErrors({});
    }, []);

    const clearErrors = useCallback((...fields: string[]) => {
        if (fields.length === 0) {
            setErrors({});
        } else {
            setErrors((prev) => {
                const next = { ...prev };
                fields.forEach((f) => delete next[f]);
                return next;
            });
        }
    }, []);

    const setError = useCallback((field: string | Record<string, string>, value?: string) => {
        if (typeof field === 'object') {
            setErrors((prev) => ({ ...prev, ...field }));
        } else if (value) {
            setErrors((prev) => ({ ...prev, [field]: value }));
        }
    }, []);

    const transform = useCallback((callback: (data: T) => any) => {
        return {
            post: (url: string, options?: FormOptions) => sendRequest('post', url, callback(data), options),
            put: (url: string, options?: FormOptions) => sendRequest('put', url, callback(data), options),
            patch: (url: string, options?: FormOptions) => sendRequest('patch', url, callback(data), options),
            delete: (url: string, options?: FormOptions) => sendRequest('delete', url, callback(data), options),
            get: (url: string, options?: FormOptions) => sendRequest('get', url, undefined, options, callback(data)),
        };
    }, [data]);

    async function sendRequest(method: string, url: string, body?: any, options?: FormOptions, queryParams?: any) {
        setProcessing(true);
        setErrors({});

        try {
            const isFormData = body instanceof FormData;
            const config: any = {};
            if (isFormData) config.headers = { 'Content-Type': 'multipart/form-data' };
            if (queryParams && method === 'get') config.params = queryParams;

            let response: any;
            if (method === 'get' || method === 'delete') {
                response = await (apiClient as any)[method](url, config);
            } else {
                response = await (apiClient as any)[method](url, body, config);
            }

            setWasSuccessful(true);
            setRecentlySuccessful(true);
            setTimeout(() => setRecentlySuccessful(false), 2000);
            options?.onSuccess?.(response);
            return response;
        } catch (err: any) {
            setWasSuccessful(false);
            if (err.response?.status === 422 && err.response?.data?.errors) {
                const serverErrors = err.response.data.errors;
                const mapped: Record<string, string> = {};
                for (const [key, messages] of Object.entries(serverErrors)) {
                    mapped[key] = Array.isArray(messages) ? (messages as string[])[0] : messages as string;
                }
                setErrors(mapped);
            }
            options?.onError?.(err.response?.data?.errors || err);
        } finally {
            setProcessing(false);
            options?.onFinish?.();
        }
    }

    return {
        data,
        setData: setField,
        setFields,
        errors,
        processing,
        recentlySuccessful,
        wasSuccessful,
        isDirty,
        reset,
        clearErrors,
        setError,
        transform,
        post: (url: string, options?: FormOptions) => sendRequest('post', url, data, options),
        put: (url: string, options?: FormOptions) => sendRequest('put', url, data, options),
        patch: (url: string, options?: FormOptions) => sendRequest('patch', url, data, options),
        delete: (url: string, options?: FormOptions) => sendRequest('delete', url, undefined, options),
        get: (url: string, options?: FormOptions) => sendRequest('get', url, undefined, options, data),
    };
}

export default useForm;
