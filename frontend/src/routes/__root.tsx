import { Toaster } from '@/components/ui/sonner';
import '@/styles/globals.css';
import { Outlet, createRootRoute } from '@tanstack/react-router';
import * as React from 'react';

import { AppSidebar } from '@/components/custom/layout/AppSidebar';
import { SiteHeader } from '@/components/custom/layout/Header';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';
import { SWR_CONFIG } from '@/config';
import { PreferencesProvider } from '@/providers/PreferencesProvider';
import { SWRConfig } from 'swr';
export const Route = createRootRoute({
  component: RootComponent,
});

function RootComponent() {
  return (
    <React.Fragment>
      <PreferencesProvider>
        <SidebarProvider
          style={
            {
              '--sidebar-width': 'calc(var(--spacing) * 72)',
              '--header-height': 'calc(var(--spacing) * 12)',
            } as React.CSSProperties
          }
        >
          <AppSidebar variant="inset" />
          <SidebarInset>
            <SiteHeader />
            <div className="flex flex-1 flex-col">
              <div className="@container/main flex flex-1 flex-col gap-2 p-4">
                <SWRConfig value={SWR_CONFIG}>
                  <Outlet />
                </SWRConfig>
              </div>
            </div>
          </SidebarInset>
        </SidebarProvider>
        <Toaster />
      </PreferencesProvider>
    </React.Fragment>
  );
}
