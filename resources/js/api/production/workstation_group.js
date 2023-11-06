import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/production/workstation/group/data',
    method: 'get',
    params: query,
  });
}

export function createWorkstationGroup(data) {
  return request({
    url: '/production/workstation/group/store',
    method: 'post',
    data,
  });
}

export function DeleteWorkstation(id) {
  return request({
    url: '/production/workstation/group/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
