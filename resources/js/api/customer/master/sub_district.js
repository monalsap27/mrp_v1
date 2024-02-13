import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/customer/master/sub_district/data',
    method: 'get',
    params: query,
  });
}

export function createOrUpdateSubDistrict(data) {
  return request({
    url: '/customer/master/sub_district/store',
    method: 'post',
    data,
  });
}

export function DeleteSubDistrict(id) {
  return request({
    url: '/customer/master/sub_district/delete',
    method: 'post',
    data: {
      id: id,
    },
  });
}

export function showByDistrict(id) {
  return request({
    url: '/customer/master/sub_district/showByDistrict',
    method: 'post',
    data: {
      id: id,
    },
  });
}
