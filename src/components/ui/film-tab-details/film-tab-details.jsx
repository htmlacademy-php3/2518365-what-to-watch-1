import React from 'react';
import filmProp from '../card/card.prop';

const renderActors = (actors) => (
  actors.map((actor, index) => {
    if (index !== (actors.length - 1)) {
      return (
        <React.Fragment key={actor}>
          {`${actor},`} <br />
        </React.Fragment>
      );
    }
    return actor;
  })
);

function FilmTabDetails(props) {
  const { starring, director, genre, released, runTime } = props.film;

  return (
    <div className="film-card__text film-card__row">
      <div className="film-card__text-col">
        <p className="film-card__details-item">
          <strong className="film-card__details-name">Director</strong>
          <span className="film-card__details-value">{director}</span>
        </p>
        <p className="film-card__details-item">
          <strong className="film-card__details-name">Starring</strong>
          <span className="film-card__details-value">
            {renderActors(starring)}
          </span>
        </p>
      </div>

      <div className="film-card__text-col">
        <p className="film-card__details-item">
          <strong className="film-card__details-name">Run Time</strong>
          <span className="film-card__details-value">{runTime}</span>
        </p>
        <p className="film-card__details-item">
          <strong className="film-card__details-name">Genre</strong>
          <span className="film-card__details-value">{genre}</span>
        </p>
        <p className="film-card__details-item">
          <strong className="film-card__details-name">Released</strong>
          <span className="film-card__details-value">{released}</span>
        </p>
      </div>
    </div>
  );
}

FilmTabDetails.propTypes = {
  film: filmProp,
};

export default FilmTabDetails;
