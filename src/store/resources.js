import Vue from "vue";

const state = {
  resources: {},
  service: '',
  exists: {},
  current: null,
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
    return state.service
  },
}

const actions = {
  init({commit}, resource) {
    commit('INIT', resource)
  },
  set({commit}, resource) {
    commit('SET', resource)
  },
  setExists({commit}, resource) {
    commit('SETEXISTS', resource)
  },
  setService({commit}, resource) {
    commit('SETSERVICE', resource)
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
  SETEXISTS(state, exists) {
    const all = {...state.exists},
          key = Object.keys(exists)[0];
    all[key] = exists[key]

    state.exists = all
  },
  SETSERVICE(state, service) {
    state.service = service
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
