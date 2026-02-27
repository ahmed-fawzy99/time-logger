import dayjs from 'dayjs';
import advancedFormat from 'dayjs/plugin/advancedFormat';
import duration from 'dayjs/plugin/duration';
import isoWeek from 'dayjs/plugin/isoWeek';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import relativeTime from 'dayjs/plugin/relativeTime';

// import timezone from 'dayjs/plugin/timezone';
// import utc from 'dayjs/plugin/utc';

// dayjs.extend(utc);
// dayjs.extend(timezone);

dayjs.extend(relativeTime);
dayjs.extend(localizedFormat);
dayjs.extend(advancedFormat);
dayjs.extend(duration);
dayjs.extend(isoWeek);

export default dayjs;
