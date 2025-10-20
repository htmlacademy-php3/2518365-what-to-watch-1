import React, {useEffect, useRef} from 'react';
import PropTypes from 'prop-types';
import { previewVideoSizes, PREVIEW_VIDEO_DELAY } from '../../../const';

function VideoPlayer(props) {
  const {src, posterUrl, isPlaying} = props;
  const videoRef = useRef();

  useEffect(() => {
    const currentPlayer = videoRef.current;
    let playTimeout = null;

    if (currentPlayer && isPlaying) {
      playTimeout = setTimeout(() => {
        currentPlayer.play();
      }, PREVIEW_VIDEO_DELAY);
    }

    return (() => {
      clearTimeout(playTimeout);
      currentPlayer.load();
    });
  }, [isPlaying]);

  return (
    <video
      src={src}
      poster={posterUrl}
      ref={videoRef}
      muted
      width={previewVideoSizes.WIDTH}
      height={previewVideoSizes.HEIGHT}
    />
  );
}

VideoPlayer.propTypes = {
  src: PropTypes.string.isRequired,
  posterUrl: PropTypes.string.isRequired,
  isPlaying: PropTypes.bool.isRequired,
};

export default VideoPlayer;
