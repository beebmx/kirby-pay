import Vue from "vue";

const state = {
  resources: {},
  service: {},
  exists: {},
  current: null,
  development: false,
}

const getters = {
  values(state) {
    return state.resources[state.current] || null
  },
  isCurrent: (state) => resource => {
    return state.current === resource
  },
  hasResource: (state) => resource => {
    return state.exists
      ? !!state.exists[resource]
      : false
  },
  getService(state) {
    return state.service.name
  },
  getServiceUrl: (state) => resource => {
    return state.service[resource]
  },
  inDevelopment(state) {
    return state.development
  },
}

const actions = {
  init({commit}, resource) {
    commit('INIT', resource)
  },
  config({commit}, {service, resources, development}) {
    commit('SERVICE', service)
    commit('EXISTS', resources)
    commit('DEVELOPMENT', development)
  },
  set({commit}, resource) {
    commit('SET', resource)
  },
}

const mutations = {
  INIT(state, resource) {
    state.current = resource
  },
  SET(state, resource) {
    const resources = {...state.resources}
    resources[state.current] = resource

    state.resources = resources
  },
  EXISTS(state, resources) {
    state.exists = resources
  },
  SERVICE(state, service) {
    state.service = service
  },
  DEVELOPMENT(state, development) {
    state.development = development
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
