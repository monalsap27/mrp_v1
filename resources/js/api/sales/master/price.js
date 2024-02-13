import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/sales/master/price/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdatePrice(data) {
  return request({
    url: '/sales/master/price/store',
    method: 'post',
    data,
  });
}

export function showByProduct(id) {
  return request({
    url: '/sales/master/price/showByProduct',
    method: 'post',
    data: {
      id: id,
    },
  });
}
