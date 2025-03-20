import { toast } from "vue3-toastify";
import axios from "axios";

export function handleApiError(error: unknown): void {
  let errorMessage = "An unexpected error occurred. Please try again later.";

  if (axios.isAxiosError(error)) {
    if (error.response?.data && error.response.data.message) {
      errorMessage = error.response.data.message;
    }
  }

  toast(errorMessage, {
    theme: "colored",
    type: "error",
    position: "top-left",
    autoClose: 4000,
    hideProgressBar: true,
  });
}
