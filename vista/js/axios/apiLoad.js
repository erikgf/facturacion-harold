const apiAxios = axios.create({
    baseURL: __URL_API,
    crossDomain: true,
    timeout: 10000,
    headers: {
      'Authorization': `Bearer ${JSON.parse(localStorage.getItem(SESSION_NAME))?.token}`,
      'Accept': 'application/json',
      "Content-Type": "application/json",
    },
    withCredentials : true
  });

  const apiAxiosPublic = axios.create({
    baseURL: __URL_API,
    crossDomain: true,
    timeout: 10000,
    headers: {
      'Accept': 'application/json',
      "Content-Type": "application/json",
    }
  });