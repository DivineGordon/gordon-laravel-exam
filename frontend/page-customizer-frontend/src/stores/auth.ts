import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/api/axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('auth_token'));

  async function register(name: string, email: string, password: string, password_confirmation: string) {
    const response = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation,
    });
    
    user.value = response.data.user;
    token.value = response.data.token;
    localStorage.setItem('auth_token', response.data.token);
  }

  async function login(email: string, password: string) {
    const response = await api.post('/login', { email, password });
    
    user.value = response.data.user;
    token.value = response.data.token;
    localStorage.setItem('auth_token', response.data.token);
  }

  async function logout() {
    await api.post('/logout');
    user.value = null;
    token.value = null;
    localStorage.removeItem('auth_token');
  }

  async function fetchUser() {
    if (!token.value) return;
    
    try {
      const response = await api.get('/user');
      user.value = response.data;
    } catch (error) {
      // Token invalid
      logout();
    }
  }

  return { user, token, register, login, logout, fetchUser };
});