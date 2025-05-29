// store/company_profile/index.js
import { defineStore } from "pinia";
import { getCompanyProfile, updateCompanyProfile } from "@store/company_profile/actions.js";
import state from "@store/company_profile/state.js";

export const useCompanyProfileStore = defineStore('CompanyProfileStore', {
    state: state,
    getters: {
        companyData: (state) => state.companyProfile.data
    },
    actions: {
        async getCompanyProfile() {
            try {
                return await getCompanyProfile(this);
            } catch (error) {
                throw error;
            }
        },
async updateCompanyProfile(companyData) {
    try {
        return await updateCompanyProfile(companyData);
    } catch (error) {
        throw error;
    }
}
    }
});