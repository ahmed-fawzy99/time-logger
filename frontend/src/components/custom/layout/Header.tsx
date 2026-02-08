import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';

import { IconChevronRight } from '@tabler/icons-react';
import { Link, useLocation } from '@tanstack/react-router';

interface Breadcrumb {
  label: string;
  path: string;
}

function formatSegmentLabel(segment: string): string {
  // Replace hyphens and underscores with spaces
  const formatted = segment.replace(/[-_]/g, ' ');
  // Capitalize first letter of each word
  return formatted
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

function getBreadcrumbs(pathname: string): Breadcrumb[] {
  const segments = pathname.split('/').filter(Boolean);
  const breadcrumbs: Breadcrumb[] = [];

  if (segments.length === 0) {
    return [{ label: 'Projects', path: '/' }];
  }

  segments.forEach((segment, index) => {
    const path = '/' + segments.slice(0, index + 1).join('/');
    const label = formatSegmentLabel(segment);

    breadcrumbs.push({ label, path });
  });

  return breadcrumbs;
}

export function SiteHeader() {
  const location = useLocation();
  const breadcrumbs = getBreadcrumbs(location.pathname);

  return (
    <header className="flex h-(--header-height) shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-(--header-height)">
      <div className="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
        <SidebarTrigger className="-ml-1 cursor-pointer" />
        <Separator
          orientation="vertical"
          className="mx-2 data-[orientation=vertical]:h-4"
        />
        <nav className="flex items-center gap-1 text-base font-medium overflow-auto">
          {breadcrumbs.map((breadcrumb, index) => {
            const isLast = index === breadcrumbs.length - 1;

            return (
              <div
                key={breadcrumb.path}
                className="flex items-center gap-1 whitespace-nowrap"
              >
                {isLast ? (
                  <span className="text-muted-foreground">
                    {breadcrumb.label}
                  </span>
                ) : (
                  <>
                    <Link
                      to={breadcrumb.path}
                      className="text-foreground hover:text-primary transition-colors"
                    >
                      {breadcrumb.label}
                    </Link>
                    <IconChevronRight className="h-4 w-4 text-muted-foreground" />
                  </>
                )}
              </div>
            );
          })}
        </nav>
      </div>
    </header>
  );
}
