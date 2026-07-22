import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import RoleFormModal from './RoleFormModal.vue'

// Mock i18n
const mockT = vi.fn((key) => key)

describe('RoleFormModal.vue', () => {
  it('renders correctly when open', () => {
    const wrapper = mount(RoleFormModal, {
      props: {
        isOpen: true
      },
      global: {
        plugins: [
          createTestingPinia({
            createSpy: vi.fn,
            initialState: {
              role: {
                permissions: [{ id: 1, name: 'Manage Users' }],
                isLoading: false,
                error: null
              }
            }
          })
        ],
        mocks: {
          $t: mockT
        },
        stubs: {
          BaseModal: {
            template: '<div class="base-modal-stub"><slot /></div>',
            props: ['modelValue', 'title']
          },
          BaseAdminInput: {
            template: '<input class="base-admin-input-stub" />',
            props: ['modelValue', 'label', 'disabled', 'required']
          },
          BaseAdminButton: {
            template: '<button class="base-admin-button-stub"><slot /></button>'
          },
          IconShieldCheck: true
        }
      }
    })

    expect(wrapper.exists()).toBe(true)
    // form should exist
    expect(wrapper.find('form').exists()).toBe(true)
  })
})
