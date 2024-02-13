import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/master/provinces/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateProvinces(data) {
  return request({
    url: '/customer/master/provinces/store',
    method: 'post',
    data,
  });
}

export function DeleteProvinces(id) {
  return request({
    url: '/customer/master/provinces/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
