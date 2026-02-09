import { ConfirmationDialog } from '@/components/custom/dialog/ConfirmationDialog';
import { Button } from '@/components/ui/button';
import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import { getTimeFrameInvoice } from '@/lib/data-access/time-frame';
import { IconInvoice } from '@tabler/icons-react';
import { useRouter } from '@tanstack/react-router';
import { useCallback } from 'react';
import { toast } from 'sonner';

interface GetInvoiceProps {
  timeFrame: TimeFrameResource;
}

export default function GetInvoice({ timeFrame }: GetInvoiceProps) {
  const router = useRouter();
  console.log(timeFrame);
  const downloadInvoice = useCallback(
    async (timeFrame: TimeFrameResource) => {
      await getTimeFrameInvoice({
        identifier: timeFrame.id,
      })
        .then((res) => {
          if (res.data.invoiceUrl) {
            window.open(res.data.invoiceUrl, '_blank');
          } else {
            toast.error('Invoice not available for this time frame.');
          }
          router.invalidate();
        })
        .catch((e) => {
          toast.error(
            e?.response?.data?.data ??
              e?.response?.data?.message ??
              e?.message ??
              'Failed to fetch invoice for this time frame.',
          );
        });
    },
    [router],
  );

  if (timeFrame.attributes.invoiceUrl) {
    return (
      <ConfirmationDialog
        title="An existing invoice was found for this time frame"
        description="You previously generated an invoice for this time frame that you can download it from the summary card.
        
        Proceeding will erase the existing invoice and generate a new one. Do you want to proceed anyways?"
        ctaText="Generate New Invoice"
        ctaVariant="default"
        successToastMessage="Invoice downloaded successfully"
        onConfirm={async () => {
          await downloadInvoice(timeFrame);
        }}
      >
        <Button variant="outline">
          <IconInvoice />
          Get Invoice
        </Button>
      </ConfirmationDialog>
    );
  }
  return (
    <Button variant="outline" onClick={() => downloadInvoice(timeFrame)}>
      <IconInvoice />
      Get Invoice
    </Button>
  );
}
