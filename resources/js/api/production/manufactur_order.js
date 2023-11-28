import request from '@/utils/request';

export function createManufacture(data) {
  return request({
    url: '/production/manufacture/store',
    method: 'post',
    data,
  });
}

export function fetchListApproval(query) {
  return request({
    url: '/production/manufacture/dataApproval',
    method: 'post',
    params: query,
  });
}
export function detailListAvailable(id) {
  return request({
    url: '/production/manufacture/detailListAvailable',
    method: 'post',
    params: {
      id: id,
    },
  });
}
export function storeApproval(data) {
  return request({
    url: '/production/manufacture/storeApproval',
    method: 'post',
    data,
  });
}
export function fetchList(query) {
  return request({
    url: '/production/manufacture/dataManufactureOrder',
    method: 'post',
    params: query,
  });
}
export function startProduction(data) {
  return request({
    url: '/production/manufacture/startProduction',
    method: 'post',
    data,
  });
}
export function timelineProgress(id) {
  return request({
    url: '/production/manufacture/timelineProgress',
    method: 'post',
    params: {
      id: id,
    },
  });
}
export function showDetailManufaturingOrder(id) {
  return request({
    url: '/production/manufacture/showDetailManufaturingOrder',
    method: 'post',
    params: {
      id: id,
    },
  });
}
export function showDetailMaterial(id) {
  return request({
    url: '/production/manufacture/showDetailMaterial',
    method: 'post',
    params: {
      id: id,
    },
  });
}
export function startWorkstation(id) {
  return request({
    url: '/production/manufacture/startWorkstation',
    method: 'post',
    params: {
      id: id,
    },
  });
}
export function pauseORFinish(data) {
  return request({
    url: '/production/manufacture/pauseORFinish',
    method: 'post',
    data,
  });
}
