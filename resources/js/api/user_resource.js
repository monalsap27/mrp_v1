import request from '@/utils/request';

export function uploadSignature(data) {
  return request({
    url: '/users/uploadSignature',
    method: 'post',
    data,
  });
}
export function ShowSignature(id) {
  return request({
    url: '/users/showSignature',
    method: 'post',
    data: {
      id: id,
    },
  });
}
