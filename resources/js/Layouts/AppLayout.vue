<template>
  <div>
    <Head :title="title" />

    <jet-banner />

    <div>
      <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto py-2 px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center h-12">
            <div class="flex items-center">
              <!-- Logo -->
              <div class="flex-shrink-0 flex items-center">
                <Link :href="'/'">
                  <jet-application-mark class="block h-9 w-auto" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <div
                v-if="$page.props.user"
                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
              >
                <jet-nav-link
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                >
                  {{ $t("Dashboard") }}
                </jet-nav-link>
              </div>
            </div>

            <input
              v-model="q"
              @keydown.enter="search()"
              type="search"
              id="search"
              class="ml-2 mr-2 w-full h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm lg:w-96 focus:ring-blue-500 focus:border-blue-500"
              :placeholder="$t('Search')"
              autocomplete="off"
            />

            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <!-- Settings Dropdown -->
              <div>
                <Link
                  :href="route('package.create')"
                  class="px-4 py-2 rounded hover:rounded-lg hover:shadow-md hover:bg-gray-50 text-sm"
                >
                  {{ $t("Create ClickRead Package") }}
                </Link>
              </div>
              <div v-if="$page.props.user" class="ml-3 relative">
                <jet-dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="$page.props.jetstream.managesProfilePhotos"
                      class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                    >
                      <img
                        class="h-8 w-8 rounded-full object-cover"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                      />
                    </button>

                    <span v-else class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                      >
                        {{ $page.props.user.name }}

                        <svg
                          class="ml-2 -mr-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      {{ $t("Manage Account") }}
                    </div>

                    <jet-dropdown-link :href="route('profile.show')">
                      {{ $t("Profile") }}
                    </jet-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <jet-dropdown-link as="button">
                        {{ $t("Log Out") }}
                      </jet-dropdown-link>
                    </form>
                  </template>
                </jet-dropdown>
              </div>
              <div v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 underline">
                  {{ $t("Log in") }}
                </Link>

                <Link
                  :href="route('register')"
                  class="ml-4 text-sm text-gray-700 underline"
                >
                  {{ $t("Register") }}
                </Link>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
              >
                <svg
                  class="h-6 w-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div v-if="$page.props.user" class="pt-2 pb-3 space-y-1">
            <jet-responsive-nav-link
              :href="route('package.create')"
              :active="route().current('package.create')"
            >
              {{ $t("Create ClickRead Package") }}
            </jet-responsive-nav-link>

            <div class="border-t border-gray-100"></div>

            <jet-responsive-nav-link
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              {{ $t("Dashboard") }}
            </jet-responsive-nav-link>
          </div>
          <div v-else class="pt-2 pb-3 space-y-1">
            <jet-responsive-nav-link
              :href="route('package.create')"
              :active="route().current('package.create')"
            >
              {{ $t("Create ClickRead Package") }}
            </jet-responsive-nav-link>

            <div class="border-t border-gray-100"></div>

            <jet-responsive-nav-link :href="route('login')">
              {{ $t("Log in") }}
            </jet-responsive-nav-link>

            <div class="border-t border-gray-100"></div>

            <jet-responsive-nav-link :href="route('register')">
              {{ $t("Register") }}
            </jet-responsive-nav-link>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200">
            <div v-if="$page.props.user" class="flex items-center px-4">
              <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex-shrink-0 mr-3"
              >
                <img
                  class="h-10 w-10 rounded-full object-cover"
                  :src="$page.props.user.profile_photo_url"
                  :alt="$page.props.user.name"
                />
              </div>

              <div>
                <div class="font-medium text-base text-gray-800">
                  {{ $page.props.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                  {{ $page.props.user.email }}
                </div>
              </div>
            </div>

            <div v-if="$page.props.user" class="mt-3 space-y-1">
              <jet-responsive-nav-link
                :href="route('profile.show')"
                :active="route().current('profile.show')"
              >
                {{ $t("Profile") }}
              </jet-responsive-nav-link>

              <div class="border-t border-gray-100"></div>

              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <jet-responsive-nav-link as="button">
                  {{ $t("Log Out") }}
                </jet-responsive-nav-link>
              </form>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="px-4 sm:px-6 lg:px-8">
          <slot name="header"></slot>
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
  props: {
    title: String,
  },

  components: {
    Head,
    JetApplicationMark,
    JetBanner,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetResponsiveNavLink,
    Link,
  },

  data() {
    return {
      showingNavigationDropdown: false,
      // loggedIn: this.$page.props.user,
      q: this.$page.props.queryParams ? this.$page.props.queryParams.q : "",
    };
  },

  methods: {
    search() {
      this.$inertia.get(route("search"), { q: this.q });
    },
    switchToTeam(team) {
      this.$inertia.put(
        route("current-team.update"),
        {
          team_id: team.id,
        },
        {
          preserveState: false,
        }
      );
    },

    logout() {
      this.$inertia.post(route("logout"));
    },
  },
});
</script>
