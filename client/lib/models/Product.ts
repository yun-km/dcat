export type ProductInfo = {
  id: number;
  title: string;
  summary: string;
  description: string;
  cover: string;
  pictures: string; 
  product_category_id: string;
  tags: string;
  user_id: number;
  created_at: string;
  updated_at: string;
}
export type Types = {
  types: Type[];
}
export type Type = {
  id?: number;
  typeName: string;
  options: Option[];

}
export type Option = {
  id?: number;
  optionName: string;
}

export type OptionSelection = {
  type: string | number;
  option: string | number;
};

export type InventoryEntry = {
  id?: number;
  productId: number;
  productItemTypeOptionId: OptionSelection[];
  price: number;
  totalQuantity: number;
};

export type InventoryEntries = InventoryEntry[];

export type ProductInventories= {
  product: ProductInfo;
  inventories: InventoryEntry[];
}