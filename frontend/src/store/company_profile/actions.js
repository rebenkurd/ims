// store/company_profile/actions.js
import axiosClient from '@/axios';
export async function getCompanyProfile(state) {
    state.companyProfile.loading = true;
    try {
        const response = await axiosClient.get('/company-profile');
        // Access the nested data property from the response
        state.companyProfile = {
            loading: false,
            data: response.data.data || {} // Match Laravel resource structure
        };
        return response.data.data; // Return just the company data
    } catch (error) {
        state.companyProfile.loading = false;
        console.error("Failed to get company profile:", error);
        throw error;
    }
}


export async function updateCompanyProfile(companyData) {
    
    // Convert companyData to work with nested objects
    for (const [key, value] of Object.entries(companyData)) {
        if (value !== null && value !== undefined) {
            if (value instanceof File) {
                companyData.append(key, value);                
            } else if (typeof value === 'object' && !(value instanceof File)) {
                companyData.append(key, JSON.stringify(value));
            } else {
                companyData.append(key, value);
            }
        }
    }

    // debugger
    
    const response = await axiosClient.post('/company-profile', companyData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
    
    return response.data;
}
