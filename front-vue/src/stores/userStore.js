import {defineStore} from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => ({
        user:null,
    }),
    getters: {
        getUser: (state) => state.user,
        isLoggedIn: (state) => !!state.user,
        //isAdmin: (state) => state.user && state.user.role === 'admin',
    },
    persist:true,
    actions: {
        setUser(user){
            this.user = user;
        },
        clearUser(){
            this.user = null;
        }
    }
});