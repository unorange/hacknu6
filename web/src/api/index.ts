import axios from "axios";
import type { AxiosRequestConfig } from "axios";
import { BASE_URL } from "@/config";
import { useAuthStore } from "@/stores/auth";

const api = axios.create({
  baseURL: BASE_URL,
});

api.interceptors.request.use((request) => {
  const { token } = useAuthStore();

  if (token) request.headers.Authorization = `Bearer ${token}`;

  return request;
});

const request = async <T>(
  config: AxiosRequestConfig
): Promise<[T, null] | [null, Error]> => {
  try {
    const { data } = await api.request<T>(config);
    return [data, null];
  } catch (throwable) {
    if (throwable instanceof Error) return [null, throwable];
    throw throwable;
  }
};

export default {
  validateDocument: (document_id: string) =>
    request<{ message: string }>({
      method: "GET",
      url: `/check-document/${document_id}`,
    }),
  login: (iin: string) =>
    request<{ message: string }>({
      method: "POST",
      url: `/single-code-auth/generate/${iin}`,
    }),
  authorize: (iin: string, otp: string) =>
    request<{ token: string; person: PersonDetails }>({
      method: "POST",
      url: "/single-code-auth/login",
      data: {
        IIN: iin,
        code: otp,
      },
    }),
  getLocations: () =>
    request<{ data: Locations }>({
      method: "GET",
      url: "/locations",
    }),
  getCons: (location_id: number) =>
    request<{
      data: Cons;
    }>({
      method: "GET",
      url: `/cons/${location_id}`,
    }),
  getDeliveryServices: (location_id: number) =>
    request<{
      data: DeliveryServices;
    }>({
      method: "GET",
      url: `/delivery-services/${location_id}`,
    }),
  calculatePrice: (
    con_id: number,
    delivery_service_id: number,
    address: string
  ) =>
    request<Calculation>({
      method: "POST",
      url: "/calculate-price",
      data: {
        con_id,
        delivery_service_id,
        address,
      },
    }),
  makeOrder: (
    address: string,
    document_id: string,
    con_id: number,
    delivery_service_id: number,
    receiver_IIN: number
  ) =>
    request<{ message: string }>({
      method: "POST",
      url: "/create-delivery",
      data: {
        address,
        document_id,
        con_id,
        delivery_service_id,
        receiver_IIN,
      },
    }),
};
