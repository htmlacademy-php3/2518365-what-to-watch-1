import React from 'react';
import {MAX_RATING} from '../../../const';

function ReviewForm() {
  const [comment, setComment] = React.useState('');
  const [rating, setRating] = React.useState(null);

  const textChangeHandler = (evt) => {
    setComment(evt.target.value);
  };
  const ratingChangeHandler = (evt) => {
    setRating(evt.target.value);
  };

  const ratingValues = [];
  for (let i = 1; i <= MAX_RATING; i++) {
    ratingValues.push(i);
  }

  ratingValues.reverse();
  return (
    <div className="add-review">
      <form action="#" className="add-review__form">
        <div className="rating">
          <div className="rating__stars">
            {
              ratingValues.map((value) => (
                <React.Fragment key={value}>
                  <input
                    className="rating__input"
                    id={`star-${value}`}
                    type="radio"
                    name="rating"
                    value={`${value}`}
                    checked={value === +rating}
                    onChange={ratingChangeHandler}
                  />
                  <label className="rating__label" htmlFor={`star-${value}`}>{`Rating ${value}`}</label>
                </React.Fragment>
              ))
            }
          </div>
        </div>

        <div className="add-review__text">
          <textarea
            className="add-review__textarea"
            name="review-text"
            id="review-text"
            placeholder="Review text"
            value={comment}
            onChange={textChangeHandler}
          >

          </textarea>
          <div className="add-review__submit">
            <button className="add-review__btn" type="submit">Post</button>
          </div>

        </div>
      </form>
    </div>
  );
}

export default ReviewForm;

