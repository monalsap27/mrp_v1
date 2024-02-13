import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/customer/data',
    method: 'get',
    params: query,
  });
}

export function createCustomer(data) {
  return request({
    url: '/customer/customer/store',
    method: 'post',
    data,
  });
}

export function DeleteCustomer(id) {
  return request({
    url: '/customer/customer/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}

export function fetchShowCustomer(id) {
  return request({
    url: '/customer/customer/fetchShowCustomer',
    method: 'post',
    data: {
      id: id,
    },
  });
}
