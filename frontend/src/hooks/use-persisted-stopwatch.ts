import localforage from 'localforage';
import { useCallback, useEffect } from 'react';
import { useStopwatch } from 'react-timer-hook';
const MS_STORE_NAME = 'tl-stopwatch-ms';

const usePersistedStopWatch = ({ interval = 500 } = {}) => {
  const {
    totalSeconds,
    milliseconds,
    seconds,
    minutes,
    hours,
    days,
    isRunning,
    start,
    pause,
    reset,
  } = useStopwatch({
    autoStart: false,
    interval,
  });

  // On mount, load the persisted time from localforage and set it as the stopwatch offset
  useEffect(() => {
    localforage
      .getItem(MS_STORE_NAME)
      .then((storedMs) => {
        if (!storedMs) return;
        const stopwatchOffset = new Date();
        stopwatchOffset.setSeconds(
          stopwatchOffset.getSeconds() + Number(storedMs) / 1000,
        );
        reset(stopwatchOffset, false);
      })
      .catch((err) => console.error('Failed to load stopwatch:', err));
  }, [reset]);

  // Persist the stopwatch time to localforage whenever totalSeconds changes
  useEffect(() => {
    if (!isRunning) return;
    localforage.setItem(MS_STORE_NAME, totalSeconds * 1000);
  }, [totalSeconds, isRunning]);

  // A hard reset that also clears the persisted time in localforage
  const hardReset = useCallback(
    (offset?: Date | undefined, newAutoStart: boolean | undefined = false) => {
      localforage.removeItem(MS_STORE_NAME);
      reset(offset, newAutoStart);
    },
    [reset],
  );

  return {
    totalSeconds,
    milliseconds,
    seconds,
    minutes,
    hours,
    days,
    isRunning,
    start,
    pause,
    reset: hardReset,
  };
};

export default usePersistedStopWatch;
