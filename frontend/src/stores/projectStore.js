import { defineStore } from 'pinia';
import api from '@/api';

export const useProjectStore = defineStore('project', {
  state: () => ({
    project: null,
    sections: [],
    answers: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchProject(projectId) {
      this.loading = true;
      this.error = null;
      try {
        const projectResponse = await api.get(`/api/projects/${projectId}`);
        this.project = projectResponse.data;
        this.answers = projectResponse.data.answers || [];
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchSections() {
      try {
        const sectionsResponse = await api.get('/api/sections');
        this.sections = Object.entries(sectionsResponse.data)
          .map(([key, value]) => ({ key, ...value }))
          .sort((a, b) => a.order - b.order);
      } catch (e) {
        this.error = e.message;
      }
    },

    async updateAnswer(sectionKey, questionKey, answerValue, isNotApplicable) {
      const answer = this.answers.find(a => a.section_key === sectionKey && a.question_key === questionKey);
      const url = answer ? `/api/answers/${answer.id}` : '/api/answers';
      const method = answer ? 'put' : 'post';

      try {
        const response = await api[method](url, {
          project_id: this.project.id,
          section_key: sectionKey,
          question_key: questionKey,
          answer_value: { value: answerValue },
          is_not_applicable: isNotApplicable,
        });

        if (answer) {
          Object.assign(answer, response.data);
        } else {
          this.answers.push(response.data);
        }
      } catch (e) {
        this.error = e.message;
      }
    },

    async saveProject() {
      // This action can be used to trigger a batch save if needed in the future.
      // For now, the save is done on each answer update.
      console.log('Project saved (manual trigger)');
    },
  },

  getters: {
    getAnswer: (state) => (sectionKey, questionKey) => {
      return state.answers.find(a => a.section_key === sectionKey && a.question_key === questionKey);
    },
  },
});
