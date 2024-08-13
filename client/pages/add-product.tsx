import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import React, { useRef, useEffect, useState } from 'react';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";
import useSWRMutation from 'swr/mutation';
import { useForm, Controller, FieldError } from 'react-hook-form';
import { useRouter } from 'next/router';
import { getFetcher, formDataFetcher2 } from "@/lib/api";
import { ToastContainer, toast } from 'react-toastify';

export const getServerSideProps: GetServerSideProps = async ({ req, res }) => {
    const session = await getSession(req, res);
  
    if (!session.user) {
      return {
        redirect: {
          destination: '/login', 
          permanent: false,
        },
      };
    }
  
    return {
      props: { user: session.user, api_token: session.api_token }, 
    };
};

export default function AddProduct({ user, api_token }: { user: UserData, api_token: string }) {
  const { trigger: categoriesTrigger, data: categoriesData } = useSWRMutation('/backed/api/categories', getFetcher);
  useEffect(() => {
    categoriesTrigger();
  }, []);

  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const coverFileInputRef = useRef<HTMLInputElement | null>(null);
  const picturesFileInputRef = useRef<HTMLInputElement | null>(null);
  // const handleButtonClick = () => {
  //   if (fileInputRef.current) {
  //     fileInputRef.current.click();
  //   }
  // };
  const handleButtonClick = (ref: React.RefObject<HTMLInputElement>) => {
    if (ref.current) {
      ref.current.click();
    }
  };
  const [selectedFile, setSelectedFile] = useState<string | null>(null);
  const [selectedFiles, setSelectedFiles] = useState<string[]>([]);
  const handleFileChange = (event:any) => {
    const file = event.target.files[0];
    if (file) {
        setSelectedFile(URL.createObjectURL(file));
    }
  };
  const handleFileChangeEvent = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || []);
    setSelectedFiles(files.map(file => URL.createObjectURL(file)));
    // handleFileChange(e);
  };


  const { control, handleSubmit, formState: { errors }, setError } = useForm();
  const { trigger: productTrigger, data: productResult, error, isMutating } = useSWRMutation('/backed/api/products', formDataFetcher2);
  const onSubmit = (data: any) => {
    data['api_token'] = api_token;

    const formData = new FormData();

    // if (data.pictures && Array.isArray(data.pictures)) {
    //   (data.pictures as File[]).forEach((file) => {
    //     formData.append('pictures[]', file);
    //   });
    // }
    
    for (const key in data) {
      if (key !== 'pictures') {
        formData.append(key, data[key]);
      } else {
        const files = Array.from(data.pictures as FileList);
        // if (data.pictures && data.pictures.length > 0) {
          files.forEach((file: File) => {
            formData.append('pictures[]', file);
          });
        // }
      }
    }

    console.log('Form Data:', data);
    formData.forEach((value, key) => {
      console.log(key, value);
    });
    
    productTrigger(data)
    .then(response => {
      // 处理成功的响应
      console.log('Product created:', response);
    })
    .catch(error => {
      // 处理错误的响应
      console.error('Error:', error);
    });
  };

  const router = useRouter();
  useEffect(() => {
    if(productResult?.result == "success") {
      toast.success('新增成功！')
      // router.reload();
    }
    if (productResult?.result === 'error') {
      Object.keys(productResult.errors).forEach((key) => {
        setError(key as keyof typeof productResult.errors as string, {
          type: 'manual',
          message: productResult.errors[key][0] 
        });
      });
    }
  },[productResult]);  

  return (
    <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
      <Head>
        <title>新增商品</title>
      </Head>
      <Container containerClass="flex flex-col max-w-screen-xl  w-full px-6 sm:flex-row">
        <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">
          <div className="font-medium flex items-center">
            <button className="p-2">
              <p>新增商品</p>
            </button>
          </div>
        </article>
        <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:w-4/5 sm:p-8 sm:mt-6">
          <p className="text-3xl font-bold mb-5 mx-4 mt-4">新增商品</p>
          <hr />
          <div className="flex flex-col sm:flex-row justify-center pt-4 sm:p-10">
            <div className="w-full">
              <form className="mx-4" onSubmit={handleSubmit(onSubmit)}>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="title" className="items-center sm:text-right col-span-2">商品名稱</label>
                  <Controller
                    name="title"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                      <input 
                        type="text" {...field} required 
                        className="col-span-10 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"
                      />
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="product_category_id" className="items-center sm:text-right col-span-2">商品種類</label>
                  <Controller
                    name="product_category_id"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                      <select 
                        {...field} required
                        className="col-span-10 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"
                      >
                        <option value="">選擇商品種類</option>
                        {categoriesData && categoriesData.map((category: any) => (
                          <option key={category.id} value={category.id}>{category.name}</option>
                        ))}
                      </select>
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="tags" className="items-center sm:text-right col-span-2">標籤</label>
                  <Controller
                    name="tags"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                      <input 
                        type="text" {...field} required
                        className="col-span-10 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"  
                      />
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="summary" className="items-center sm:text-right col-span-2">摘要</label>
                  <Controller
                    name="summary"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                      <input 
                        type="text" {...field} required
                        className="col-span-10 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"  
                      />
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="description" className="items-center sm:text-right col-span-2">描述</label>
                  <Controller
                    name="description"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                      <input 
                        type="text" {...field} required
                        className="col-span-10 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"  
                      />
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                  <label htmlFor="cover" className="items-center sm:text-right col-span-2">封面圖</label>
                  <Controller
                    name="cover"
                    control={control}
                    defaultValue={null}
                    render={({ field }) => (
                        <>
                            <input
                                type="file"
                                id="cover_file_input"
                                ref={coverFileInputRef}
                                aria-label="cover"
                                style={{ display: 'none' }}
                                onChange={(e) => {
                                  const files = e.target.files;
                                  if (files && files[0]) {
                                      field.onChange(files[0]);
                                      handleFileChange(e);
                                  }
                                }}
                            />
                            <button
                                type="button"
                                onClick={() => handleButtonClick(coverFileInputRef)}
                            >
                                {selectedFile ? (
                                    <img src={selectedFile} alt="Selected file preview" className="w-20 h-20 sm:w-28 sm:h-28 text-large" />
                                ) : (
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
                                        <path fillRule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clipRule="evenodd" />
                                    </svg>
                                )}
                            </button>
                        </>
                    )}
                  />
                </div>
                <div className="flex flex-col pb-4 sm:pb-10 sm:gap-8 sm:items-center sm:grid sm:grid-cols-12">
                <label htmlFor="pictures" className="items-center sm:text-right col-span-2">圖片組</label>
                  <Controller
                    name="pictures"
                    control={control}
                    defaultValue={[]}
                    render={({ field }) => (
                      <>
                      <input
                        type="file"
                        id="pictures_file_input"
                        ref={picturesFileInputRef}
                        aria-label="pictures"
                        style={{ display: 'none' }}
                        multiple
                        onChange={(e) => {
                          handleFileChangeEvent(e); 
                          field.onChange(e.target.files); 
                        }}
                      />
                      <button
                        type="button"
                        onClick={() => handleButtonClick(picturesFileInputRef)}
                      >
                        {selectedFiles.length > 0 ? (
                          <div className="flex space-x-2">
                            {selectedFiles.map((file, index) => (
                              <img
                                key={index}
                                src={file}
                                alt={`Selected file preview ${index}`}
                                className="w-20 h-20 sm:w-28 sm:h-28 text-large"
                              />
                            ))}
                          </div>
                        ) : (
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
                            <path fillRule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clipRule="evenodd" />
                          </svg>
                        )}
                      </button>
                    </>
                    )}
                  />
                </div>

                <div className="flex-col flex justify-center mb-0 sm:mt-8">
                  <button type="submit" className="bg-blue-500 text-white py-2 px-4 rounded">
                    新增商品
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </Container>
    </Layout>
  );
}
