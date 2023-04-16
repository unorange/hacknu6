<script setup lang="ts">
import { h, ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import {
  NAvatar,
  NText,
  NModal,
  NAlert,
  NSteps,
  NStep,
  NButton,
  NInput,
  NSpace,
  NSelect,
  NAutoComplete,
  NRadioGroup,
  NRadio
} from 'naive-ui'
import type { StepsProps, SelectRenderTag, SelectRenderLabel } from 'naive-ui'
import { useAuthStore } from '../stores/auth'
import { useAsync } from '../composables/useAsync'
import api from '../api'
import type { Locations, PersonDetails, Calculation, Cons, DeliveryServices } from '../types'

const route = useRoute()

const document = route.params.id

const { run: checkDocument, active: checkingDocument } = useAsync(api.validateDocument)

const isValidDocument = ref(false)

onMounted(async () => {
  const [data, error] = await checkDocument(document as string)

  if (error) return (isValidDocument.value = false)

  isValidDocument.value = data
})

const authStore = useAuthStore()

const iin = ref('')
const otp = ref('')
const validIIN = ref(false)

const currentStep = ref<number | null>(1)
const currentStatus = ref<StepsProps['status']>('process')
const locations = ref<Locations | []>([])

const personDetails = ref<PersonDetails | null>(null)

const { active: pendingVerification, run: verify } = useAsync(api.login)
const { active: loadingAuthorization, run: authorize } = useAsync(api.authorize)
const { active: loadingLocations, run: loadLocations } = useAsync(api.getLocations)
const { active: loadingCons, run: loadCons } = useAsync(api.getCons)
const { active: loadingDeliveryServices, run: loadDeliveryServices } = useAsync(
  api.getDeliveryServices
)
const { active: calculatingPrice, run: loadCalculation } = useAsync(api.calculatePrice)
const { active: makingDelivery, run: makeDelivery } = useAsync(api.makeOrder)

const loading = computed(() => {
  return (
    pendingVerification.value ||
    loadingAuthorization.value ||
    calculatingPrice.value ||
    makingDelivery.value
  )
})

const person = computed(() => {
  if (!personDetails.value) return null

  return {
    fullName:
      `${personDetails.value.last_name} ${personDetails.value.first_name} ${personDetails.value.middle_name}`.toLowerCase()
  }
})

const selectedCity = ref('')
const selectedAddress = ref('')
const selectedAdditionalAddress = ref('')
const calculated = ref<Calculation | null>(null)

const flatCities = computed(() => {
  if (!locations.value.length) return []

  return locations.value
    .map((location) => location.cities)
    .flat()
    .map((city) => city.name)
})

const showSuccessPaymentModal = ref(false)
const showErrorPaymentModal = ref(false)
const representativeIIN = ref('')

const next = async () => {
  if (currentStep.value === 1) {
    if (!validIIN.value) {
      const [data, error] = await verify(iin.value)

      if (error) return

      validIIN.value = true

      return
    } else {
      const [data, error] = await authorize(iin.value, otp.value)

      if (error) return

      authStore.setToken(data.token)

      personDetails.value = data.person
      currentStep.value = 2

      const [locationsData, locationsError] = await loadLocations()

      if (locationsError) return

      locations.value = locationsData.data

      return
    }
  }

  if (currentStep.value === 2) {
    const [data, error] = await loadCalculation(
      Number(selectedCon.value),
      Number(selectedDeliveryService.value),
      `${selectedCity.value}, ${selectedAddress.value}, ${selectedAdditionalAddress.value}`
    )

    if (error) return

    calculated.value = data

    currentStep.value = 3

    return
  }

  if (currentStep.value === 3) {
    const [data, error] = await makeDelivery(
      `${selectedCity.value}, ${selectedAddress.value}, ${selectedAdditionalAddress.value}`,
      document as string,
      Number(selectedCon.value),
      Number(selectedDeliveryService.value),
      acceptedBy.value === 'me' ? iin.value : representativeIIN.value
    )

    if (error) {
      showErrorPaymentModal.value = true
    } else {
      showSuccessPaymentModal.value = true
    }

    return
  }
}

const autocompleteOptions = computed(() => {
  if (!selectedCity.value.length || !flatCities.value) return []

  return flatCities.value
    .filter((city) => city.toLowerCase().includes(selectedCity.value.toLowerCase()))
    .map((city) => ({ label: city, value: city }))
})

const isAvailableCity = computed(() => {
  if (!selectedCity.value || !flatCities.value.length) return false

  return flatCities.value.includes(selectedCity.value)
})

const isDisabledSubmitButton = computed(() => {
  if (currentStep.value === 1) return iin.value.length !== 12

  if (currentStep.value === 2) {
    return !isAvailableCity.value || !selectedAddress.value
  }

  return false
})

const cons = ref<Cons>([])
const selectedCon = ref(null)

const deliveryServices = ref<DeliveryServices>([])
const selectedDeliveryService = ref(null)

watch(isAvailableCity, async (newValue, oldValue) => {
  if (newValue === oldValue) return

  const region = locations.value.find((location) =>
    location.cities.find((item) => item.name === selectedCity.value)
  )

  if (!region) return

  const [data, error] = await loadCons(region.id)

  if (error) return

  cons.value = data.data

  const [deliveryServicesData, deliveryServicesError] = await loadDeliveryServices(region.id)

  if (deliveryServicesError) return

  deliveryServices.value = deliveryServicesData.data
})

const renderSingleSelectTag: SelectRenderTag = ({ option }) => {
  return h(
    'div',
    {
      style: {
        display: 'flex',
        alignItems: 'center'
      }
    },
    [
      h(NAvatar, {
        src: option.image_url as string,
        round: true,
        size: 24,
        style: {
          marginRight: '12px'
        }
      }),
      option.label as string
    ]
  )
}
const renderLabel: SelectRenderLabel = (option) => {
  return h(
    'div',
    {
      style: {
        display: 'flex',
        alignItems: 'center'
      }
    },
    [
      h(NAvatar, {
        src: option.image_url as string,
        round: true,
        size: 'small'
      }),
      h(
        'div',
        {
          style: {
            marginLeft: '12px',
            padding: '4px 0'
          }
        },
        [
          h('div', null, [option.label as string]),
          h(
            NText,
            { depth: 3, tag: 'div' },
            {
              default: () => `${option.base_coefficient}₸ за километр`
            }
          )
        ]
      )
    ]
  )
}

const acceptedBy = ref<'me' | 'representative'>('me')
</script>

<template>
  <template v-if="!checkingDocument">
    <template v-if="!isValidDocument">
      <div class="p-2">
        <n-alert title="Ошибка" type="error">
          Запрашиваемый документ не найден или уже был выдан
        </n-alert>
      </div>
    </template>
    <template v-else>
      <div class="flex flex-col gap-3">
        <div>
          <h1 class="text-2xl">Выдача документа</h1>
          <span class="text-xl text-neutral-700"> #{{ document }} </span>
        </div>
        <div class="my-2 w-full">
          <n-steps
            size="small"
            :current="(currentStep as number)"
            class="w-full"
            :status="currentStatus"
          >
            <n-step title="Проверка" />
            <n-step title="Доставка" />
            <n-step title="Оплата" />
          </n-steps>
        </div>
        <template v-if="currentStep === 1">
          <div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500">ИИН</label>
              <NInput
                v-model:value="iin"
                placeholder="030920550610"
                required
                :readonly="validIIN"
              />
            </div>
            <div class="mb-2" v-if="validIIN">
              <label class="text-base mb-1 text-zinc-500"> Одноразовый код </label>
              <NInput v-model:value="otp" placeholder="Код из СМС" required />
            </div>
          </div>
        </template>
        <template v-else-if="currentStep === 2">
          <template v-if="person">
            <span class="text-lg">
              Здравствуйте,
              <span class="capitalize">
                {{ person.fullName }}
              </span>
            </span>
          </template>
          <div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500">Город</label>
              <n-auto-complete
                required
                v-model:value="selectedCity"
                :input-props="{
                  autocomplete: 'disabled'
                }"
                :options="autocompleteOptions"
                placeholder="Введите город"
              />
            </div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500">Адрес</label>
              <n-input
                required
                v-model:value="selectedAddress"
                :disabled="!isAvailableCity"
                placeholder="Введите адрес"
              />
            </div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500"> Подъезд, этаж, квартира </label>
              <n-input
                required
                v-model:value="selectedAdditionalAddress"
                :disabled="!isAvailableCity"
                placeholder="(дополнительно)"
              />
            </div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500"> ЦОН </label>
              <n-select
                required
                v-model:value="selectedCon"
                :options="
                  cons.map((item) => ({
                    label: item.address,
                    value: item.id
                  }))
                "
                :loading="loadingCons"
                :disabled="loadingCons"
                placeholder="Отделение ЦОН"
              />
            </div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500"> Сервис доставки </label>
              <n-select
                v-model:value="selectedDeliveryService"
                :options="
                  deliveryServices.map((item) => ({
                    label: item.name,
                    value: item.id,
                    base_coefficient: item.base_coefficient,
                    image_url: item.image_url
                  }))
                "
                :render-label="renderLabel"
                :render-tag="renderSingleSelectTag"
                :loading="loadingDeliveryServices"
                :disabled="loadingDeliveryServices"
                placeholder="Выберите сервис доставки"
              />
            </div>
            <div class="mb-2">
              <label class="text-base mb-1 text-zinc-500"> Получатель документа </label>
              <n-radio-group v-model:value="acceptedBy" name="radiogroup">
                <n-space>
                  <n-radio value="me" label="Я сам" />
                  <n-radio value="representative" label="Представитель" />
                </n-space>
              </n-radio-group>
            </div>
            <div class="mb-2" v-if="acceptedBy == 'representative'">
              <label class="text-base mb-1 text-zinc-500"> ИИН Представителя </label>
              <n-input v-model:value="representativeIIN" placeholder="ИИН" />
            </div>
          </div>
        </template>
        <template v-else-if="currentStep === 3">
          <template v-if="calculated">
            <h1 class="text-xl mb-2">Стоимость доставки: {{ calculated.price }}₸</h1>
          </template>
        </template>
        <n-button
          :disabled="isDisabledSubmitButton"
          strong
          secondary
          round
          type="primary"
          :loading="loading"
          @click="next()"
        >
          {{ currentStep === 3 ? 'Оплатить' : 'Продолжить' }}
        </n-button>
      </div>
    </template>
    <n-modal v-model:show="showSuccessPaymentModal">
      <n-alert title="Заказ оформлен" type="success"> Оплата прошла успешно </n-alert>
    </n-modal>
    <n-modal v-model:show="showErrorPaymentModal">
      <n-alert title="Прозошла ошибка" type="error"> Не удалось оформить заказ </n-alert>
    </n-modal>
  </template>
</template>
