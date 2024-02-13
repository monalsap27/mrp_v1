import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/deposit/data',
    method: 'get',
    params: query,
  });
}

export function createDeposit(data) {
  return request({
    url: '/customer/deposit/store',
    method: 'post',
    data,
  });
}

export function showByCustomer(id) {
  return request({
    url: '/customer/deposit/showByCustomer',
    method: 'post',
    data: {
      id: id,
    },
  });
}

export function dataTransaction(id) {
  return request({
    url: '/customer/deposit/dataTransaction',
    method: 'post',
    data: {
      id: id,
    },
  });
}
