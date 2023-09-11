import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/product/master/categories/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateCategories(data) {
  return request({
    url: '/product/master/categories/store',
    method: 'post',
    data,
  });
}

export function DeleteCategories(id) {
  return request({
    url: '/product/master/categories/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
