import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/workstation/data',
    method: 'get',
    params: query,
  });
}
export function createWorkstation(data) {
  return request({
    url: '/production/workstation/store',
    method: 'post',
    data,
  });
}
export function DeleteWorkstation(id) {
  return request({
    url: '/production/workstation/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function fetchWorkstation(id) {
  return request({
    url: '/production/workstation/show',
    method: 'get',
    params: {
      id: id,
    },
  });
}
export function fetchWorkstationDetail(id) {
  return request({
    url: '/production/workstation/showDetail',
    method: 'get',
    params: {
      id: id,
    },
  });
}
export function fetchWorkstationByGroup(id) {
  return request({
    url: '/production/workstation/showByGroup',
    method: 'get',
    params: {
      id: id,
    },
  });
}
