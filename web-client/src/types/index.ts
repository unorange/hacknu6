export type PersonDetails = {
  id: number
  IIN: number
  last_name: string
  first_name: string
  middle_name: string
  eng_first_name: string
  eng_surname: string
  date_of_birth: string
  created_at: string
  updated_at: string
}

export type Locations = Array<{
  id: number
  name: string
  cities: Array<{
    id: number
    name: string
    district_id: number
  }>
}>

export type Cons = Array<{
  id: number
  long: number
  lat: number
  address: string
  region_id: number
}>

export type DeliveryServices = Array<{
  id: number
  name: string
  base_coefficient: number
  image_url: string
}>

export type Calculation = {
  distance: number
  price: number
  tariff: number
}

export type Courier = {
  id: number
  first_name: string
  last_name: string
  phone: string
  status: string
  delivery_service_id: number
  created_at: string
  updated_at: string
}

export type CourierStatus = {
  status: string
  delivery: {
    id: number
    client_id: number
    deliveryman_id: number
    starting_point: string
    end_point: string
    IIN: number
    status: string
    start_time: string
    end_time: any
    created_at: string
    updated_at: string
    document_id: string
    price: number
    receiver_IIN: number
    operator_id: number
  }
}
