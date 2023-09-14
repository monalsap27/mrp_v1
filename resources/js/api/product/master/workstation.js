import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/product/master/workstation/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateWorkstation(data) {
  return request({
    url: '/product/master/workstation/store',
    method: 'post',
    data,
  });
}

export function DeleteWorkstation(id) {
  return request({
    url: '/product/master/workstation/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
