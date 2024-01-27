const apiAxios = axios.create({
    baseURL: __URL_API,
    crossDomain: true,
    timeout: 5000,
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
    timeout: 5000,
    headers: {
      'Accept': 'application/json',
      "Content-Type": "application/json",
    }
  });