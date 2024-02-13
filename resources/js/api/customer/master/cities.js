import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/master/cities/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateCities(data) {
  return request({
    url: '/customer/master/cities/store',
    method: 'post',
    data,
  });
}

export function DeleteCities(id) {
  return request({
    url: '/customer/master/cities/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function showByProvince(id) {
  return request({
    url: '/customer/master/cities/showByProvince',
    method: 'post',
    data: {
      id: id,
    },
  });
}
