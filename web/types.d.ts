type PersonDetails = {
  id: number;
  IIN: number;
  last_name: string;
  first_name: string;
  middle_name: string;
  eng_first_name: string;
  eng_surname: string;
  date_of_birth: string;
  created_at: string;
  updated_at: string;
};

type Locations = Array<{
  id: number;
  name: string;
  cities: Array<{
    id: number;
    name: string;
    district_id: number;
  }>;
}>;

type Cons = Array<{
  id: number;
  long: number;
  lat: number;
  address: string;
  region_id: number;
}>;

type DeliveryServices = Array<{
  id: number;
  name: string;
  base_coefficient: number;
  image_url: string;
}>;

type Calculation = {
  distance: number;
  price: number;
  tariff: number;
};
