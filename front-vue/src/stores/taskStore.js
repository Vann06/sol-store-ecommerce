import { defineStore } from "pinia";
import axios from "axios";

const apiUrl = import.meta.env.VITE_APP_API_URL;

export const useTaskStore = defineStore("task", {
    state: () => ({
        tasks: [],
        newTask: "",
    }),
    actions: {
        async getTasks() {
            try {
              const response = await axios.get(apiUrl);
              this.tasks = response.data;
            } catch (error) {
              console.error("Error fetching tasks: "+error);  
            }
        },
        async addTask() {
            if (this.newTask.trim() === '') return;
            try {
               const response = await axios.post(apiUrl, {
                    task: this.newTask                    
                }); 
                this.tasks.push(response.data);
                this.newTask = "";
            } catch (error) {
                console.error("Error adding task: "+error);
            }
        },
        async updateTask(task) {
            try {
                await axios.put(`${apiUrl}/${task.id}`, task);
            } catch (error) {
                console.error("Error updating task: "+error);
            }
        },
        async deleteTask(id) {
            try {
                await axios.delete(`${apiUrl}/${id}`);
                this.tasks = this.tasks.filter((task) => task.id !== id);
            } catch (error) {
                console.error("Error deleting task: "+error);
            }
        }
    }
})