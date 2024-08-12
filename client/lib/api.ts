export const postFetcher = async (url: string, { arg }: any) => {
  const response = await fetch(url, {
    method: 'POST',
    credentials: "include",
    headers: {
      'Content-Type': 'application/json',
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
