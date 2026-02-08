import {
  IconBrandGithub,
  IconCalendar,
  IconSettings,
} from '@tabler/icons-react';
import * as React from 'react';

import { NavMain } from '@/components/custom/layout/NavMenu';
import { NavSecondary } from '@/components/custom/layout/NavSecondary';
import {
  Sidebar,
  SidebarContent,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Link } from '@tanstack/react-router';
import { Clock1Icon } from 'lucide-react';

const data = {
  navMain: [
    {
      title: 'Projects',
      url: '/',
      icon: IconCalendar,
      altPathnames: ['/projects'],
    },
    {
      title: 'Preferences',
      url: '/preferences',
      icon: IconSettings,
    },
  ],

  navSecondary: [
    {
      title: 'GitHub',
      url: 'https://github.com/ahmed-fawzy99/time-logger',
      target: '_blank',
      icon: IconBrandGithub,
    },
  ],
};

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  return (
    <Sidebar collapsible="offcanvas" {...props}>
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton
              asChild
              className="data-[slot=sidebar-menu-button]:p-1.5!"
            >
              <Link to="/">
                <Clock1Icon className="size-5!" />
                <span className="text-base font-semibold">Time Logger</span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>
      <SidebarContent>
        <NavMain items={data.navMain} />
        <NavSecondary items={data.navSecondary} className="mt-auto" />
      </SidebarContent>
    </Sidebar>
  );
}
