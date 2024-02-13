import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/master/district/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateDistrict(data) {
  return request({
    url: '/customer/master/district/store',
    method: 'post',
    data,
  });
}

export function DeleteDistrict(id) {
  return request({
    url: '/customer/master/district/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function showByCity(id) {
  return request({
    url: '/customer/master/district/showByCity',
    method: 'post',
    data: {
      id: id,
    },
  });
}
