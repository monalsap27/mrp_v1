import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/master/postal_code/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdatePostalCode(data) {
  return request({
    url: '/customer/master/postal_code/store',
    method: 'post',
    data,
  });
}

export function DeletePostalCode(id) {
  return request({
    url: '/customer/master/postal_code/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}
export function showBySubDistrict(id) {
  return request({
    url: '/customer/master/postal_code/showBySubDistrict',
    method: 'post',
    data: {
      id: id,
    },
  });
}
