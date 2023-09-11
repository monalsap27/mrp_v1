import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/product/master/material/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateMaterial(data) {
  return request({
    url: '/product/master/material/store',
    method: 'post',
    data,
  });
}

export function DeleteMaterial(id) {
  return request({
    url: '/product/master/material/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
