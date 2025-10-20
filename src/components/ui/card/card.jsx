import React from 'react';
import filmProp from './card.prop';
import VideoPlayer from '../video-player/video-player';
import PropTypes from 'prop-types';
import {Link} from 'react-router-dom';
import {AppRoute} from '../../../const';

function Card(props) {
  const {film, mouseEnterHandler, mouseLeaveHandler, isActive} = props;
  const {name, previewImage, id, previewVideoLink} = film;
  return (
    <article
      className="small-film-card catalog__films-card"
      onMouseEnter={mouseEnterHandler}
      onMouseLeave={mouseLeaveHandler}
    >
      <Link to={`${AppRoute.FILM}/${film.id}`}>
        <div className="small-film-card__image">
          <VideoPlayer
            src={previewVideoLink}
            posterUrl={previewImage}
            isPlaying={isActive}
          />
        </div>
      </Link>
      <h3 className="small-film-card__title">
        <Link to={`${AppRoute.FILM}/${id}`} className="small-film-card__link">{name}</Link>
      </h3>
    </article>
  );
}

Card.propTypes = {
  film: filmProp,
  mouseEnterHandler: PropTypes.func.isRequired,
  mouseLeaveHandler: PropTypes.func.isRequired,
  isActive: PropTypes.bool.isRequired,
};

export default Card;
