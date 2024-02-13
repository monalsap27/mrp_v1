import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/sales/master/tenor/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateTenor(data) {
  return request({
    url: '/sales/master/tenor/store',
    method: 'post',
    data,
  });
}

export function DeleteTenor(id) {
  return request({
    url: '/sales/master/tenor/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
