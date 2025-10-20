import React from 'react';
import filmProp from '../card/card.prop';
import { MAX_ACTORS_COUNT,  RatingLevels } from '../../../const';
import { transformRating } from '../../../utils/utils';

const getRatingLevel = (rating) => {
  const tresholds = Object.keys( RatingLevels).map((el) => +el);
  const currentTreshold = tresholds.find((treshold) => rating < treshold);

  return RatingLevels[currentTreshold];
};

const renderActors = (actors) => {
  if (actors.length <= MAX_ACTORS_COUNT) {
    return actors.join(', ');
  }
  return `${actors.slice(0, MAX_ACTORS_COUNT).join(', ')} and others`;
};

function FilmTabOverview(props) {
  const {rating, scoresCount, description, director, starring} = props.film;

  return (
    <React.Fragment>
      <div className="film-rating">
        <div className="film-rating__score">{transformRating(rating)}</div>
        <p className="film-rating__meta">
          <span className="film-rating__level">{getRatingLevel(rating)}</span>
          <span className="film-rating__count">{`${scoresCount} ratings`}</span>
        </p>
      </div>

      <div className="film-card__text">
        <p>{description}</p>

        <p className="film-card__director"><strong>{`Director: ${director}`}</strong></p>

        <p className="film-card__starring">
          <strong>
            {`Starring: ${renderActors(starring)}`}
          </strong>

        </p>
      </div>
    </React.Fragment>
  );
}

FilmTabOverview.propTypes = {
  film: filmProp,
};

export default FilmTabOverview;
