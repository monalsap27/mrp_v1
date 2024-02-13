import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/sales/master/bank/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateBank(data) {
  return request({
    url: '/sales/master/bank/store',
    method: 'post',
    data,
  });
}

export function DeleteBank(id) {
  return request({
    url: '/sales/master/bank/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
