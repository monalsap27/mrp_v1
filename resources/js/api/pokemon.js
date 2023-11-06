import request from '@/utils/request';

export function fetchList(query) {
  const myHeaders = new Headers();
  myHeaders.append('x-api-key', `TfQDTqGP732ibJonOyCNx35k3LAmoP8D1sHvCrm0`);
  return request({
    url: 'https://pokeapi.co/api/v2/ability',
    method: 'get',
    params: query,
    headers: myHeaders,
  });
}
