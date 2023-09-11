import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/product/master/type/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateType(data) {
  return request({
    url: '/product/master/type/store',
    method: 'post',
    data,
  });
}

export function DeleteType(id) {
  return request({
    url: '/product/master/type/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
