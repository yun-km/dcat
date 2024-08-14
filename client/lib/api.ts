export const postFetcher = async (url: string, { arg }: any) => {
  const response = await fetch(url, {
    method: 'POST',
    credentials: "include",
    headers: {
      'Content-Type': 'application/json',
       "Authorization": `Bearer ${arg?.api_token}`
    },
    body: JSON.stringify(arg),
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || 'An error occurred');
  }

  return response.json();
};
export const getFetcher = async (url: string, { arg }: any) => {
  const response = await fetch(url, {
    method: 'GET',
    credentials: "include",
    headers: {
      'Content-Type': 'application/json',
    },
  });
  console.log(response.status); // 打印状态码
  console.log(response.url); // 打印实际请求的URL

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || 'An error occurred');
  }

  return response.json();
};

export const formDataFetcher = async (url: string, api_token: string, formData: FormData) => {
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      "Authorization": `Bearer ${api_token}`
    },
    body: formData,
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || 'An error occurred');
  }

  return response.json();
};

export const formDataFetcher2 = async (key: string, { arg }: any) => {
  const formData = new FormData();
  
  for (const key in arg) {
    if (key === 'pictures[]') {
      const files = arg[key] as FileList;
      Array.from(files).forEach(file => {
        formData.append('pictures[]', file);
      });
    } else if (key !== 'api_token') {
      formData.append(key, arg[key]);
    }
  }

  formData.forEach((value, key) => {
    console.log(key, value);
  });

  console.log('Form Data:', arg);
  console.log('Form url:', key);


  const response = await fetch(key, {
    method: 'POST',
    headers: {
      "Authorization": `Bearer ${arg.api_token}`
    },
    body: formData, 
  });
  console.log(response.status); 
  console.log(response.url); 

  const contentType = response.headers.get('content-type');
  
  if (contentType && contentType.indexOf('application/json') !== -1) {
    return response.json();
  } else {
    const errorText = await response.text();
    console.error('Error Response:', errorText);
    throw new Error('Server returned non-JSON response.');
  }

  if (!response.ok) {
    const errorData = await response.json();
    // throw new Error(errorData.message || 'An error occurred');
    return errorData;
  }

  return response.json();
};
