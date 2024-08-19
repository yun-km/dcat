import {  
  Navbar,   
  NavbarBrand,  
  NavbarContent,   
  NavbarItem,   
  NavbarMenuToggle,  
  NavbarMenu,  
  NavbarMenuItem
} from "@nextui-org/navbar";

import {  
  Dropdown,  
  DropdownTrigger, 
  DropdownMenu,  
  DropdownItem
} from "@nextui-org/dropdown";

import {Avatar} from "@nextui-org/avatar";
import {Button} from "@nextui-org/button";
import {Link} from "@nextui-org/link";

import {Logo} from "@/components/Logo";
import { UserData } from "@/lib/models/User";
import { postFetcher } from "@/lib/api";
import useSWRMutation from 'swr/mutation';
import { useRouter } from 'next/router';
import {useEffect} from 'react';

export function TopNavbar({ user }: { user: UserData }) {
  const { trigger, data, error, isMutating } = useSWRMutation('/api/logout', postFetcher);
  const router = useRouter();
  const handleLogout = async (data: any) => {
    await trigger();
  };

  useEffect(() => {
    if (data?.result == "success") {
      router.reload();
    }
  }, [data, router]);

  const menuItems = [
    "Profile",
    "Seller",
    "Log Out",
  ];
  return (
    <Navbar isBordered className="sm:fixed">
      <NavbarContent className="sm:hidden" justify="start">
        <NavbarMenuToggle />
      </NavbarContent>

      <NavbarContent className="sm:hidden pr-3" justify="center">
        <NavbarBrand>
          <Logo />
          <p className="font-bold text-inherit">ACME</p>
        </NavbarBrand>
      </NavbarContent>

      <NavbarContent className="hidden sm:flex gap-1 items-end" justify="center">
        <NavbarBrand>
          <Logo />
          <p className="font-bold text-inherit">ACME</p>
        </NavbarBrand>

        <NavbarItem>
          <Link color="foreground" href="#" className="hover:bg-secondary-100 py-2 px-4 rounded">
            Features
          </Link>
        </NavbarItem>
        <NavbarItem isActive>
          <Link href="#" aria-current="page" className="bg-secondary-500 text-secondary-50 py-2 px-4 rounded">
            Customers
          </Link>
        </NavbarItem>
        <NavbarItem>
          <Link color="foreground" href="#" className="hover:bg-secondary-100 py-2 px-4 rounded">
            Integrations
          </Link>
        </NavbarItem>
      </NavbarContent>

      <NavbarContent as="div" className="items-end mb-3" justify="end">
        {!user ? (
          <NavbarItem>
            <Button as={Link} color="secondary" href="#" variant="flat" radius="full" size="sm">
              Sign Up
            </Button>
          </NavbarItem>
        ) : (
          <Dropdown placement="bottom-end">
            <DropdownTrigger>
              <div style={{ display: 'flex', alignItems: 'center' }}>
                <Avatar
                  isBordered
                  as="button"
                  className="transition-transform m-2"
                  color="secondary"
                  name="Jason Hughes"
                  size="sm"
                  src={`/backed/avatars/${user.avatar}`}
                />
                <p className="m-2">{user.name}</p>
              </div>
            </DropdownTrigger>
            <DropdownMenu aria-label="Profile Actions">
              <DropdownItem key="profile" textValue="Profile" className="h-14 gap-2">
                <Link href="/profile">
                  <p className="font-semibold">個人資訊</p>
                </Link>
              </DropdownItem>
              <DropdownItem key="seller" textValue="Seller" className="h-14 gap-2">
                <Link href="/seller-products">
                  <p className="font-semibold">賣家中心</p>
                </Link>
              </DropdownItem>
              <DropdownItem key="logout" textValue="Log Out" color="danger">
                <button type="button" onClick={handleLogout}>
                  Log Out
                </button>
              </DropdownItem>
            </DropdownMenu>
          </Dropdown>
        )}
      </NavbarContent>

      <NavbarMenu>
        {menuItems.map((item, index) => (
          <NavbarMenuItem key={`${item}-${index}`}>
            <Link
              className="w-full"
              color={
                index === 2 ? "warning" : index === menuItems.length - 1 ? "danger" : "foreground"
              }
              href="#"
              size="lg"
            >
              {item}
            </Link>
          </NavbarMenuItem>
        ))}
      </NavbarMenu>
    </Navbar>
  );
}
