<script setup lang="ts">
import { defineComponent, ref, computed, watch, onMounted } from 'vue'
import { useLocalStorage, useTimeoutPoll } from '@vueuse/core'
import {
  type FormInst,
  useMessage,
  NForm,
  NFormItem,
  NInput,
  NButton,
  NRow,
  NCol,
  NSelect,
  NCard,
  NTag,
  NModal,
  NInputGroup
} from 'naive-ui'
import api from '@/api'
import { useAsync } from '@/composables/useAsync'

const formRef = ref<FormInst | null>(null)
const message = useMessage()

const formValue = ref({
  phone: '',
  password: ''
})

const savedPhone = useLocalStorage('phone', '')
const savedCourier = useLocalStorage<any>('courier', {})
// const selectedStatus = ref('')

const loggedIn = computed(() => {
  return !!savedPhone.value
})

const rules = {
  phone: {
    required: true,
    message: 'Введите номер телефона',
    trigger: ['input']
  },
  password: {
    required: true,
    message: 'Введите пароль',
    trigger: ['input']
  }
}

function isEmpty(obj: Record<string, any>): boolean {
  return Object.keys(obj).length === 0
}

const { run: login, active: loadingLogin } = useAsync(api.authorizeCourier)

const handleValidateClick = (e: MouseEvent) => {
  e.preventDefault()
  formRef.value?.validate(async (errors) => {
    if (!errors) {
      const [data, error] = await login(formValue.value.phone)
      if (error) {
        message.error('Произошла ошибка')
        return
      }

      savedPhone.value = formValue.value.phone
      savedCourier.value = data

      console.log(data)
    } else {
      console.log(errors)
    }
  })
}

const { run: updateStatus, active: loadingUpdateStatus } = useAsync(api.changeStatus)

const changeStatus = async (phone: string) => {
  const prevStatus = JSON.parse(JSON.stringify(savedCourier.value.status))

  const [data, error] = await updateStatus(phone)
  if (error) {
    message.error('Произошла ошибка')
    return
  }
  if (prevStatus === 'active') {
    savedCourier.value.status = 'inactive'
  } else if (prevStatus === 'inactive') {
    savedCourier.value.status = 'active'
  }
}

const statusButtonType = computed(() => {
  switch (savedCourier.value.status) {
    case 'active':
      return 'success'
    case 'inactive':
      return 'error'
    case 'in_order':
      return 'warning'
    default:
      return 'warning'
  }
})

const STATUSES = {
  active: 'Активен',
  inactive: 'Неактивен',
  in_order: 'В заказе'
} as any

const timesFetched = ref(0)

const currentDelivery = useLocalStorage<any>('currentDelivery', {})

const { run: loadStatus, active: loadingStatus } = useAsync(api.checkoutStatus)

const { resume: startPollingKeys, pause } = useTimeoutPoll(async () => {
  if (timesFetched.value >= 500) {
    pause()
  }
  const [data, error] = await loadStatus(savedPhone.value)
  if (error) {
    message.error('Произошла ошибка')
    return
  } else {
    savedCourier.value.status = data.status
    currentDelivery.value = data.delivery
  }
  timesFetched.value += 1
}, 5_000)

watch(savedPhone, async (phone) => {
  if (phone) {
    startPollingKeys()
  }
})

onMounted(() => {
  if (savedPhone.value) {
    startPollingKeys()
  }
})

const showModal = ref(false)

const { run: updateDeliveryStatus, active: loadingUpdateDeliveryStatus } = useAsync(
  api.updateDeliveryStatus
)

const { run: updateDeliveryStatusAndPhone, active: loadingUpdateDeliveryStatusAndPhone } = useAsync(
  api.inputDeliveryCode
)

const updateInputCode = async (delivery_id: number, code: string) => {
  const [data, error] = await updateDeliveryStatusAndPhone(delivery_id, savedPhone.value, code)
  if (error) {
    message.error('Произошла ошибка')
    return
  }
  currentDelivery.value = data
  closeModal()
}

const openModal = async (delivery_id: number) => {
  showModal.value = true
  const [data, error] = await updateDeliveryStatus(delivery_id, savedPhone.value)

  if (error) {
    message.error('Произошла ошибка')
    return
  }
}

const code = ref('')
const closeModal = () => {
  showModal.value = false
  code.value = ''
}

const showHistoryModal = ref(false)

const history = ref<any[]>([])

const { run: loadHistory, active: loadingHistory } = useAsync(api.getDeliveryHistory)

const getHistory = async () => {
  const [data, error] = await loadHistory(savedPhone.value)
  if (error) {
    message.error('Произошла ошибка')
    return
  }
  history.value = data
}

const openHistoryModal = async () => {
  showHistoryModal.value = true
  await getHistory()
}

const closeHistoryModal = () => {
  showHistoryModal.value = false
}

const formatStatus = (status: string) => {
  if (status === 'in_order') {
    return 'В заказе'
  }
  if (status === 'active') {
    return 'Активен'
  }
  if (status === 'inactive') {
    return 'Неактивен'
  }
  if (status === 'in_delivery') {
    return 'В доставке'
  }
  if (status === 'delivery_id') {
    return 'Доставлен'
  }
}
</script>

<template>
  <div class="flex h-screen">
    <div class="mx-auto">
      <div v-if="!loggedIn" class="mt-24">
        <h1 class="text-2xl mb-4 font-bold w-80">Вход</h1>
        <n-form
          ref="formRef"
          :model="formValue"
          :rules="rules"
          :style="{
            maxWidth: '640px'
          }"
          label-width="auto"
        >
          <n-form-item label="Телефон" path="phone">
            <n-input v-model:value="formValue.phone" placeholder="Номер телефона" />
          </n-form-item>
          <n-form-item label="Пароль" path="password">
            <n-input v-model:value="formValue.password" type="password" placeholder="Пароль" />
          </n-form-item>

          <!-- <n-row :gutter="[0, 24]"> -->
          <n-button round @click="handleValidateClick" :loading="loadingLogin"> Войти </n-button>
          <!-- </n-row> -->
        </n-form>
      </div>
      <template v-else>
        <div class="flex items-center gap-2">
          <div
            v-if="!isEmpty(savedCourier)"
            class="px-4 py-2.5 bg-gray-50 items-center flex justify-center rounded w-fit"
          >
            {{ savedCourier.first_name }}
            {{ savedCourier.last_name }}
          </div>
          <NButton
            :disabled="savedCourier.status === 'in_order' || loadingUpdateStatus"
            :loading="loadingUpdateStatus"
            tertiary
            size="large"
            :type="statusButtonType"
            @click="changeStatus(savedPhone)"
          >
            {{ STATUSES[savedCourier.status] }}
          </NButton>
        </div>
        <div class="w-full items-center text-center my-2 justify-center">
          <n-button @click="openHistoryModal" size="small" quaternary> История заказов </n-button>
        </div>
        <div class="mt-2">
          <n-card
            v-if="currentDelivery?.id"
            :title="`Доставка #${currentDelivery.id}`"
            embedded
            :bordered="false"
          >
            {{ currentDelivery.starting_point }} - {{ currentDelivery.end_point }}
            <br />
            {{ currentDelivery.start_time }}

            <template #footer>
              <div class="flex w-full items-end justify-between gap-x-12">
                <NTag :bordered="false" round type="info">
                  {{ formatStatus(currentDelivery.status) }}
                </NTag>
                <n-button size="small" @click="openModal(currentDelivery.id)" quaternary>
                  Обновить статус
                </n-button>
              </div>
            </template>
          </n-card>
          <n-modal v-model:show="showModal">
            <n-card
              style="width: 600px"
              title="Обновление статуса"
              :bordered="false"
              size="huge"
              role="dialog"
              aria-modal="true"
            >
              <n-input-group>
                <n-input
                  v-model:value="code"
                  type="text"
                  placeholder="Код"
                  :loading="loadingUpdateDeliveryStatus"
                  :disabled="loadingUpdateDeliveryStatus"
                />
                <n-button
                  :style="{ width: '33%' }"
                  @click="updateInputCode(currentDelivery.id, code)"
                  :loading="loadingUpdateDeliveryStatusAndPhone"
                  :disabled="loadingUpdateDeliveryStatusAndPhone"
                >
                  Обновить
                </n-button>
              </n-input-group>

              <template #footer>
                <n-button @click="closeModal()">Закрыть</n-button>
              </template>
            </n-card>
          </n-modal>
          <n-modal v-model:show="showHistoryModal">
            <div :key="item.id" v-for="item in history">
              <n-card :title="`Доставка #${item.id}`" embedded :bordered="false">
                {{ item.starting_point }} - {{ item.end_point }}
                <br />
                {{ item.start_time }}

                <template #footer>
                  <div class="flex w-full items-end justify-between gap-x-12">
                    <NTag :bordered="false" round type="info">
                      {{ formatStatus(item.status) }}
                    </NTag>
                  </div>
                </template>
              </n-card>
              <hr />
            </div>
          </n-modal>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped></style>
