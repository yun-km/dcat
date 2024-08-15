import Layout from "@/components/Layout";
import Container from "@/components/Container";
import Head from 'next/head';
import React, { useRef, useEffect, useState } from 'react';
import { GetServerSideProps } from "next";
import { getSession } from "@/lib/session";
import { UserData } from "@/lib/models/User";
import { ProductInfo, Types, InventoryEntries, InventoryEntry,OptionSelection } from "@/lib/models/Product";
import useSWRMutation from 'swr/mutation';
import useSWR from 'swr';
import { useForm, Controller, FieldError, useFieldArray } from 'react-hook-form';
import { useRouter } from 'next/router';
import { getFetcher, formDataFetcher2, postFetcher } from "@/lib/api";
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
  const [selectedStep, setSelectedStep] = useState<number>(3);
  const [productInfo, setProductInfo] = useState<ProductInfo | null>(null);
  const [productTypes, setProductTypes] = useState<Types | null>(null);
  const renderStepContent = () => {
    switch (selectedStep) {
      case 1:
        return <AddProductItem api_token={api_token} setSelectedStep={setSelectedStep} setProductInfo={setProductInfo} productInfo={productInfo} />;
      case 2:
        return <AddProductItemType api_token={api_token} setSelectedStep={setSelectedStep} setProductTypes={setProductTypes} productTypes={productTypes} />;
      case 3:
        return <AddProductTypeOptions />;
      default:
        return <AddProductTypeOptions  />;
    }
  };

  return (
    <Layout mainClass="flex w-full h-auto items-center justify-center" user={user}>
      <Head>
        <title>新增商品</title>
      </Head>
      <Container containerClass="flex flex-col max-w-screen-xl  w-full px-6 sm:flex-row">
        <article className="w-full text-wrap p-5 sm:w-1/5 sm:mt-6">

        <ProductWizardSidebar selectedStep={selectedStep} setSelectedStep={setSelectedStep} />

        </article>
        <div className="w-full border rounded-xl p-5 bg-white gap-3 sm:w-4/5 sm:p-8 sm:mt-6">
          <p className="text-3xl font-bold mb-5 mx-4 mt-4">新增商品</p>
          <hr />
          <div className="flex flex-col sm:flex-row justify-center pt-4 sm:p-10">
            <div className="w-full">
              
            {renderStepContent()}
            </div>
          </div>
        </div>
      </Container>
    </Layout>
  );
}

function ProductWizardSidebar({ 
  selectedStep,
  setSelectedStep
} : {
  selectedStep: number;
  setSelectedStep: React.Dispatch<React.SetStateAction<number>>;
}) 
{
  return (
    <nav
      className="flex flex-col px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50"
      aria-label="Breadcrumb"
    >
      <ol className="flex flex-col space-y-2">
        <li className={`flex items-center ${selectedStep === 1 ? 'bg-blue-100 font-bold text-blue-600' : ''}`}>
          <button
            onClick={() => setSelectedStep(1)}
            className="flex items-center text-sm font-medium text-gray-700 hover:text-blue-600"
          >
            <svg
              className="w-3 h-3 mr-2.5"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
            </svg>
            Step 1
          </button>
        </li>
        <li className={`flex items-center ${selectedStep === 2 ? 'bg-blue-100 font-bold text-blue-600' : ''}`}>
          <button
            onClick={() => setSelectedStep(2)}
            className="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2"
          >
            <svg
              className="w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            Step 2
          </button>
        </li>
        <li className={`flex items-center ${selectedStep === 3 ? 'bg-blue-100 font-bold text-blue-600' : ''}`} aria-current="page">
          <button
            onClick={() => setSelectedStep(3)}
            className="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2"
          >
            <svg
              className="w-3 h-3 mx-1 text-gray-400"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 6 10"
            >
              <path
                stroke="currentColor"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                d="m1 9 4-4-4-4"
              />
            </svg>
            Step 3
          </button>
        </li>
      </ol>
    </nav>
  );
}

export function AddProductItem({ 
  api_token,
  setSelectedStep,
  setProductInfo,
  productInfo
}: { 
  api_token: string,
  setSelectedStep: React.Dispatch<React.SetStateAction<number>>; 
  setProductInfo: React.Dispatch<React.SetStateAction<ProductInfo | null>>; 
  productInfo?: ProductInfo | null;
}) 
{
  const { trigger: categoriesTrigger, data: categoriesData } = useSWRMutation('/backed/api/categories', getFetcher);
  useEffect(() => {
    categoriesTrigger();
  }, []);

  const coverFileInputRef = useRef<HTMLInputElement | null>(null);
  const picturesFileInputRef = useRef<HTMLInputElement | null>(null);
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

      
  useEffect(() => {
    console.log('selectedFiles on load:', selectedFiles);
  }, [selectedFiles]);

  const { control, handleSubmit, formState: { errors }, setError } = useForm({
    defaultValues: productInfo || {} 
  });
  const { trigger: productTrigger, data: productResult, error, isMutating } = useSWRMutation('/backed/api/products', formDataFetcher2);
  const onSubmit = (data: any) => {
    data['api_token'] = api_token;
    if(productInfo) {
      data['product_id'] = productInfo.id;
    }

    const formData = new FormData();
    
    for (const key in data) {
      if (key !== 'pictures') {
        formData.append(key, data[key]);
      } else {
        if (selectedFiles.length > 0) {
          const files = Array.from(data.pictures as FileList);
          files.forEach((file: File) => {
            formData.append('pictures[]', file);
          });
        }
      }
    }

    console.log('Form Data:', data);
    formData.forEach((value, key) => {
      console.log(key, value);
    });
    
    productTrigger(data)
  };

  const router = useRouter();
  useEffect(() => {
    if(productResult?.result == "success") {
      // toast.success('新增成功！')
      // router.reload();
      setProductInfo(productResult?.product)
      setSelectedStep(2);
    }
    if (productResult?.result === 'error') {
      Object.keys(productResult.errors).forEach((key) => {
        setError(key as keyof ProductInfo, {
          type: 'manual',
          message: productResult.errors[key][0] 
        });
      });
    }
  },[productResult]);  

  return (
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
          defaultValue={productInfo?.cover ?? undefined}
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
                      ) : productInfo?.cover ? (
                        <img src={`/backed/images/products/${productInfo?.cover}`} alt="Selected file preview" className="w-20 h-20 sm:w-28 sm:h-28 text-large" />
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
          defaultValue={productInfo?.pictures ? JSON.stringify(productInfo.pictures) : undefined}
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
              className="flex space-x-2"
              onClick={() => handleButtonClick(picturesFileInputRef)}
            >
              {selectedFiles.length > 0 ? (
                selectedFiles.map((file, index) => (
                  <img
                    key={index}
                    src={file}
                    alt={`Selected file preview ${index}`}
                    className="w-20 h-20 sm:w-28 sm:h-28"
                  />
                ))
              ) : productInfo?.pictures && JSON.parse(productInfo.pictures).length > 0 ? (
                JSON.parse(productInfo.pictures).map((file: string, index: number) => (
                  <img
                    key={index}
                    src={`/backed/images/products/${file}`}
                    alt={`Existing file ${index}`}
                    className="w-20 h-20 sm:w-28 sm:h-28"
                  />
                ))
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
  );
}
const fakeProductTypes = [
  {
    id: 1,
    typeName: "Color",
    options: ["Red", "Blue", "Green"]
  },
  {
    id: 2,
    typeName: "Size",
    options: ["Small", "Medium", "Large"]
  },
  {
    id: 3,
    typeName: "Size",
    options: ["Small", "Medium", "Large"]
  }
];

type ProductOption = {
  color: string;
  size: string;
  price: number;
  quantity: number;
};
export function AddProductTypeOptions() {
  const productId = 22;
  const fetcher = (url: string) => fetch(url).then(res => res.json());

  const { data: typeOptionsData } = useSWR(`/backed/api/product-type-options/${productId}`, fetcher);
  const { data: savedInventories, mutate: refreshSavedInventories } = useSWR(`/backed/api/product-option-inventories/${productId}`, fetcher);
  const { trigger: saveInventory } = useSWRMutation('/backed/api/product-option-inventories', postFetcher);

  const { control, handleSubmit, reset } = useForm<{ inventoryEntries: InventoryEntry[] }>({
    defaultValues: {
      inventoryEntries: [
        {
          productItemTypeOptionId: [],
          price: 0,
          totalQuantity: 0,
          productId: productId
        }
      ]
    },
  });

  const { fields, append } = useFieldArray({
    control,
    name: 'inventoryEntries',
  });

  const onSubmit = async (data: any) => {
    console.log('Submitted Data:', data);
    for (let entry of data.inventoryEntries) {
      await saveInventory(entry);
    }
    refreshSavedInventories();
    reset(); 
  };

  const handleSaveEntry = async (entry: any) => {
     saveInventory(entry);
    refreshSavedInventories();
  };

  return (
    <div>
      <form onSubmit={handleSubmit(onSubmit)}>
        {fields.map((field, index) => (
          <div key={field.id} className="flex flex-col gap-3">
            {/* <h3 className="text-xl font-bold">新增存貨</h3> */}
            {typeOptionsData && typeOptionsData.map((type: any) => (
              <div key={type.id} className="flex gap-3 items-center">
                <label >{type.typeName}</label>
                <Controller
                  control={control}
                  name={`inventoryEntries.${index}.productItemTypeOptionId`}
                  render={({ field }) => (
                    <select
                      className="w-auto text-sm border-0 border-b-2 border-gray-200 focus:outline-none focus:border-black p-2"
                      value={field.value.find((item: OptionSelection) => item.type === type.id)?.option || ''}
                      onChange={(e) => {
                        const selectedOption = parseInt(e.target.value);
                        const existingOptionIndex = field.value.findIndex((item: OptionSelection) => item.type === type.id);
                        if (existingOptionIndex > -1) {
                          field.onChange([
                            ...field.value.slice(0, existingOptionIndex),
                            { type: type.id, option: selectedOption },
                            ...field.value.slice(existingOptionIndex + 1),
                          ]);
                        } else {
                          field.onChange([...field.value, { type: type.id, option: selectedOption }]);
                        }
                      }}
                    >
                      <option value="">選擇{type.typeName}</option>
                      {type.options.map((option: any) => (
                        <option key={option.id} value={option.id}>
                          {option.optionName}
                        </option>
                      ))}
                    </select>
                  )}
                />
              </div>
            ))}
            <div className="flex gap-3 items-center">
              <label >價格</label>
              <Controller
                control={control}
                name={`inventoryEntries.${index}.price`}
                render={({ field }) => (
                  <input
                    type="number"
                    {...field}
                    placeholder="價格"
                    className="w-auto text-sm border-0 border-b-2 border-gray-200 focus:outline-none focus:border-black p-2"
                  />
                )}
              />
            </div >
            <div className="flex gap-3 items-center pb-5">
              <label >數量</label>
              <Controller
                control={control}
                name={`inventoryEntries.${index}.totalQuantity`}
                render={({ field }) => (
                  <input
                    type="number"
                    {...field}
                    placeholder="數量"
                    className="w-auto text-sm border-0 border-b-2 border-gray-200 focus:outline-none focus:border-black p-2"
                  />
                )}
              />
            </div>
          </div>
        ))}

        {/* <button type="button" onClick={() => append({ productId, productItemTypeOptionId: [], price: 0, totalQuantity: 0 })}>
          新增規格组合
        </button> */}
        <button type="submit" className="flex px-3 py-2 text-xs font-medium rounded-lg gap-1 text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white">
          <svg className="w-[16px] h-[16px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h14m-7 7V5"/>
          </svg>
          新增存貨
        </button>
      </form>
      <br/>
      
      <table className=" w-full text-gray-500">
        <thead className="text-gray-700 uppercase bg-gray-50">
          <tr>
            <th className="w-1/2 px-4 py-2">規格</th>
            <th className="w-1/4 px-4 py-2">價格</th>
            <th className="w-1/4 px-4 py-2">數量</th>
          </tr>
        </thead>
        <tbody>
          {savedInventories && savedInventories.map((inventory: InventoryEntry, index: number) => (
            <tr key={index} className="bg-white border-b">
              <td className="px-4 py-2">
                {inventory.productItemTypeOptionId.map((detail: any, idx: number) => (
                  <div key={idx}>
                    <strong>{detail.typeName}:</strong> {detail.optionName}
                  </div>
                ))}
              </td>
              <td className="px-4 py-2 text-center">
                <input
                  type="number"
                  defaultValue={inventory.price}
                  onBlur={(e) => handleSaveEntry({ ...inventory, price: parseInt(e.target.value) })}
                  className="w-2/3  text-center text-gray-700 bg-gray-50 border border-gray-300 rounded-lg"
                />
              </td>
              <td className="px-4 py-2 text-center">
                <input
                  type="number"
                  defaultValue={inventory.totalQuantity}
                  onBlur={(e) => handleSaveEntry({ ...inventory, totalQuantity: parseInt(e.target.value) })}
                   className="w-2/3  text-center text-gray-700 bg-gray-50 border border-gray-300 rounded-lg"
                />
              </td>
            </tr>
          ))}
        </tbody>
      </table>

    </div>
  );
}


export function AddProductItemType({
  api_token,
  productTypes,
  setProductTypes,
  setSelectedStep
}:{
  api_token: string
  productTypes?: Types | null;
  setProductTypes: React.Dispatch<React.SetStateAction<Types | null>>; 
  setSelectedStep: React.Dispatch<React.SetStateAction<number>>; 
}) {
  const { control, handleSubmit, register, setError } = useForm<Types>({
    defaultValues:{
      types: productTypes?.types || [{ id: undefined, typeName: '', options: [{ id: undefined, optionName: '' }] }]
    }
  });
  
  
  const { fields: typeFields, append: appendType } = useFieldArray({
      control,
      name: 'types'
  });

  const { trigger, data, isMutating } = useSWRMutation('/backed/api/products-types-options', postFetcher);

  const onSubmit = (data: any) => {
    data['api_token'] = api_token
    data['product_id'] = 22
    console.log('Form Data:', data);
    trigger(data)
  };

  useEffect(() => {
    if(data?.result == "success") {
      // toast.success('新增成功！')
      // router.reload();
      setProductTypes({ types: data?.types });
      setSelectedStep(3);
    }
    if (data?.result === 'error') {
      Object.keys(data.errors).forEach((key) => {
        setError(key as keyof Types, {
          type: 'manual',
          message: data.errors[key][0] 
        });
      });
    }
  },[data]);  

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col ">
        {typeFields.map((typeField, typeIndex) => (
            <div key={typeField.id} className="pb-10 ">
                <div className="flex items-center space-x-4">
                    <label htmlFor={`typeName-${typeIndex}`} className="sm:text-right">規格{typeIndex + 1}名稱</label>
                    <input
                        {...register(`types.${typeIndex}.typeName`)}
                        placeholder="Type Name"
                        id={`typeName-${typeIndex}`}
                        className="flex-1 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"
                    />
                    <Controller
                        control={control}
                        name={`types.${typeIndex}.options`}
                        render={({ field }) => (
                            <button
                                type="button"
                                onClick={() => field.onChange([...field.value, { name: '', isActive: true }])}
                                className="flex px-3 py-2 text-xs font-medium rounded-lg gap-1 text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white"
                                >
                                  <svg className="w-[16px] h-[16px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h14m-7 7V5"/>
                                  </svg>

                                新增選項
                            </button>
                        )}
                    />
                </div>

                <Controller
                    control={control}
                    name={`types.${typeIndex}.options`}
                    render={({ field }) => (
                      <>
                        <div className="grid grid-cols-2 gap-6 p-8">
                            {field.value.map((option, optionIndex) => (
                                <div key={optionIndex} className="flex items-center space-x-4">
                                    <label htmlFor={`option-${typeIndex}-${optionIndex}`} className="sm:text-right">選項{optionIndex + 1}</label>
                                    <input
                                        {...register(`types.${typeIndex}.options.${optionIndex}.optionName`)}
                                        placeholder="Option Name"
                                        className="flex-1 border-0 border-b-2 border-gray-200 text-sm p-2.5 bg-transparent focus:outline-none focus:border-black"
                                    />
                                </div>
                            ))}
                        </div>
                        <hr/>
                      </>

                    )}
                />
            </div>
        ))}
        <div className="flex justify-between">
          <button
            type="submit"
            className="flex px-3 py-2 text-xs font-medium rounded-lg gap-1 text-white bg-blue-700 hover:bg-blue-800"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="w-[16px] h-[16px]" fill="none" viewBox="0 0 24 24" strokeWidth={2} >
                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
              </svg>

                儲存規格
          </button>
          <button
              type="button"
              onClick={() => appendType({ id: undefined, typeName: '', options: [{ id: undefined, optionName: ''}] })}
              className="flex px-3 py-2 text-xs font-medium rounded-lg gap-1 text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white"
              >
                <svg className="w-[16px] h-[16px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 17h6m-3 3v-6M4.857 4h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857H4.857A.857.857 0 0 1 4 9.143V4.857C4 4.384 4.384 4 4.857 4Zm10 0h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857h-4.286A.857.857 0 0 1 14 9.143V4.857c0-.473.384-.857.857-.857Zm-10 10h4.286c.473 0 .857.384.857.857v4.286a.857.857 0 0 1-.857.857H4.857A.857.857 0 0 1 4 19.143v-4.286c0-.473.384-.857.857-.857Z"/>
                </svg>
                  新增規格
            </button>
        </div>
    </form>
);

}