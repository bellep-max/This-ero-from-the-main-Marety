import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

apiClient.interceptors.response.use(
    (response) => {
        if (response.data && typeof response.data === 'object' && 'success' in response.data && 'data' in response.data) {
            response.data = response.data.data;
        }
        return response;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export async function getCsrfCookie(): Promise<void> {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
}

export default apiClient;
